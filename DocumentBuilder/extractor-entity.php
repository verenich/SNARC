<?php

require_once("./APIs/qpath/src/qp.php");

class entityExtractor {

	private $entities;
	private $entity_match_limit;

	public function __construct($entity_match_limit)
	{
		$this->entity_match_limit = $entity_match_limit;
		$this->entities           = array();
		$this->cache              = new SimpleCache();	
	}

	function getEntities($alchemy_entities, $zemanta_entities) {
		
		//Normalize confidence for each result set		
		if (!empty($alchemy_entities)) $alchemy_entities = $this->normalize($alchemy_entities, "relevance");
		if (!empty($zemanta_entities)) $zemanta_entities = $this->normalize($zemanta_entities, "relevance");

		//unifying the relevancy key name between Zemanta and Alchemy
		$zemanta_entities = $this->unifyZemantaObject($zemanta_entities);
		$alchemy_entities = $this->unifyAlchemyObject($alchemy_entities);
		//combining the result of slicing the two arrays
		$this->entities = array_merge($zemanta_entities,$alchemy_entities);
		//sort the result on the specified relevancy in ascending order (most accurate first)
		usort($this->entities , array($this, "relevancySort"));
		//filtering on the entity values in the combined array
		$this->entities  = $this->filterValues($this->entities);
		$this->addWikipediaData();

		return $this->entities;
	}

	function addWikipediaData() {
		foreach($this->entities as $entity) {
			if (isset($entity->links) && !empty($entity->links)) { 
				foreach($entity->links as $link) {
					$linkTitle = is_array($link) ? $link["title"] : $link->title;
					$linkType  = is_array($link) ? $link["type"]  : $link->type;
					if ($linkType == 'wikipedia') {
						$result = array();
						if($wikipedia_decoded = $this->cache->get_cache('wikipedia:'.$linkTitle)){
							$result = $wikipedia_decoded;
						} else {
							$arrayedTitle  = explode(" ", $linkTitle);
							$searchTerm    = implode("_", $arrayedTitle);
							$url           = 'http://en.wikipedia.org/w/api.php?action=opensearch&search='.urlencode($searchTerm).'&format=xml&limit=1';
							$xml           = simplexml_load_file($url);

							$description = $xml->Section->Item->Description ?  $xml->Section->Item->Description : "";
							$result      = json_encode($description);
							$this->cache->set_cache('wikipedia:'.$linkTitle , $result);
						}
						$result = json_decode($result);
						if($result && isset($result->{0})) $link->description = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', preg_replace('/[^(\x20-\x7F)]*/','',$result->{0}));
					}
				}
			}
		}
	}

	function filterValues($array) {
		$array_size     = count($array);
		for($first_loop = 0; $first_loop < $array_size ; $first_loop++)
		{
			for($second_loop = 0; $second_loop < $array_size; $second_loop++)
			{	
				$checkedSimilarity = true;
				if($second_loop != $first_loop && isset($array[$first_loop]) && isset($array[$second_loop])) {
					$source_entityName = $array[$first_loop]->entity;
					$target_entityName = $array[$second_loop]->entity;
					$source_text       = $array[$first_loop]->text[0];
					$target_text       = $array[$second_loop]->text[0];

					similar_text($source_text,$target_text, $entityNameSimilarity);

					if ( ($entityNameSimilarity / 100) >= $this->entity_match_limit) {
						$array[$first_loop]->count++;
						$array[$first_loop]->links = $this->mergeLinks($array[$second_loop]->links,$array[$first_loop]->links);
						$array[$first_loop]->text  = array_unique(array_merge($array[$second_loop]->text,$array[$first_loop]->text));
						$array[$second_loop] = null; 
					} 
				} 
			}
		}
		return array_values(array_filter($array));
	}

	function normalize($array, $key) {
		$maximum = $array[0]->{$key}; 
		foreach ($array as $entity) {
			$entity->$key = $entity->$key / $maximum;
		}
		return $array;
	}

	function relevancySort($first_item,$second_item) {
		if ($first_item->relevance == $second_item->relevance) return 0;
		return ($first_item->relevance > $second_item->relevance) ? -1 : 1;
	}

	function mergeLinks($source_array, $target_array) {

		$result = $links = array();
		foreach($source_array as $link) {
			$url =  is_array($link) ? $link["url"] : $link->url;
			if ( filter_var($url, FILTER_VALIDATE_URL) == TRUE ) {
				$links[]  = $url;
				$result[] = $link;
			}
		}
		foreach ($target_array as $link) {
			$url =  is_array($link) ? $link["url"] : $link->url;
			if (filter_var($url, FILTER_VALIDATE_URL) == TRUE && !in_array($url, $links)) {
				$links[]  = $url;
				$result[] = $link;
			}
		}
		return $result;
	}

	function unifyZemantaObject($array) {
		foreach ($array as $entity) {
			$entityName = $entityCount = $relevance  = "";
			$entityType = $entityText  = $entityLink = array();

			$entityName = $entity->anchor;
			if (isset($entity->target) && count($entity->target) > 0 ) $entityName = $entity->target[0]->title;
			foreach ($entity->entity_type as $key=>$value) {
				$types = array_filter(explode("/", $value));
				$entityType = array_merge($entityType,$types);
			}
			$entity->type      = array_filter(array_unique($entityType));
			$entity->relevance = $entity->relevance * $entity->confidence;
			$entity->count     = 1;
			$entity->text      = array($entity->anchor);
			$entity->entity    = $entityName;
			$entity->links     = isset($entity->target) ? $entity->target : array();

			unset($entity->confidence, $entity->entity_type, $entity->target,$entity->anchor);
		}
		return $array;
	}

	function unifyAlchemyObject($array) {
		foreach ($array as $entity) {
			$entityName = $entityCount = $relevance  = "";
			$entityType = $entityText  = $entityLink = array();
			//The value of the entity examined is the text if no disambiguation is found
			$entityName   = $entity->text;
			$entityType[] = $entity->type;
			//check if there is any disambiguation
			if (isset($entity->disambiguated)) {
				$entityName = $entity->disambiguated->name;
				unset($entity->disambiguated->name);
				if (isset($entity->disambiguated->subType))  {
					$entity->type = array_filter(array_unique(array_merge($entityType,$entity->disambiguated->subType)));
					unset($entity->disambiguated->subType);
				}
				foreach ($entity->disambiguated as $key => $value) {
					$entityLink[] = array(
						"title" => $entity->text,
						"type"  => $key,
						"url"   => $value);
				}
				unset($entity->disambiguated);
			}
			$entity->text   = array($entity->text);
			$entity->entity = $entityName;
			$entity->links  = $entityLink;
			$entity->count  = 1;
		}
		return $array;
	}
}

?>
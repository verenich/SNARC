<?php

class keywordExtractor {

	private $max_keywords;
	private $restrict_threshold;
	private $threshold_limit;
	private $keywords;
	private $keyword_match_limit;

	public function __construct($max_keywords, $restrict_threshold,$keyword_match_limit)
	{
		$this->max_keywords        = $max_keywords;
		$this->restrict_threshold  = $restrict_threshold;	
		$this->keyword_match_limit = $keyword_match_limit;
		$this->keywords            = array();
		$this->threshold_limit     = 0;
	}

	function getKeywords($alchemy_keywords, $zemanta_keywords) {
		
		//Normalize confidence for each result set		
		if (!empty($alchemy_keywords)) $alchemy_keywords = $this->normalize($alchemy_keywords, "relevance");
		if (!empty($zemanta_keywords)) $zemanta_keywords = $this->normalize($zemanta_keywords, "confidence");
		
		//remove strings that are similar in the alchemy array
		$alchemy_keywords = $this->filterValues($alchemy_keywords);

		//Stabailzing array count in case any array was empty
		$alchemyCount = count($alchemy_keywords) > 0 ? count($alchemy_keywords) : $this->max_keywords;
		$zemantaCount = count($zemanta_keywords) > 0 ? count($zemanta_keywords) : $this->max_keywords;
		//finding the maximum number of keywords to return
		$number_of_keywords = min($this->max_keywords, min($alchemyCount,$zemantaCount));
		//unifying the relevancy key name between Zemanta and Alchemy
		$zemanta_keywords = $this->unifyZemantaObject($zemanta_keywords);
		//combining the result of slicing the two arrays
		$this->keywords = array_merge(array_slice($alchemy_keywords, 0, $number_of_keywords),array_slice($zemanta_keywords, 0, $number_of_keywords));
		//sort the result on the specified relevancy in ascending order (most accurate first)
		usort($this->keywords , array($this, "relevancySort"));
		//filtering on the keyword values in the combined array
		$this->keywords  = $this->filterValues($this->keywords );
		//check if threshold limit is needed and apply it
		if ($this->restrict_threshold) array_walk($this->keywords , array($this,"checkTreshold"));

		return array_values($this->keywords);
		
	}

	function filterValues($array) {
		$array_size     = count($array);
		for($first_loop = 0; $first_loop < $array_size ; $first_loop++)
		{
			for($second_loop = 0; $second_loop < $array_size; $second_loop++)
			{	
				$checkedSimilarity = true;
				if($second_loop != $first_loop && isset($array[$first_loop]) && isset($array[$second_loop])) {

					$source_text = $array[$first_loop]->text  = $this->first_two_words($array[$first_loop]->text);
					$target_text = $array[$second_loop]->text = $this->first_two_words($array[$second_loop]->text);

					similar_text($source_text,$target_text, $similarity);

					if ( ($similarity / 100) >= $this->keyword_match_limit) {
						if ($array[$first_loop]->relevance >= $array[$second_loop]->relevance) $array[$second_loop] = null; 
						else $array[$first_loop] = null;				
					} 
				} 
			}
		}
		return array_filter($array);
	}

	function normalize($array, $key) {
		$maximum = $array[0]->{$key}; 
		foreach ($array as $keyword) {
			$keyword->$key = $keyword->$key / $maximum;
		}
		return $array;
	}

	function first_two_words($array_element) {
		$pieces = explode(" ", $array_element);
		return (implode(" ", array_splice($pieces, 0, 2)));
	}

	function relevancySort($first_item,$second_item) {
		if ($first_item->relevance == $second_item->relevance) return 0;
		return ($first_item->relevance > $second_item->relevance) ? -1 : 1;
	}

	function checkTreshold($value,$key) {
		if ($value->relevance < $this->threshold_limit) $this->keywords[$key] = null;
	}

	function unifyZemantaObject($array) {
		foreach ($array as $keyword) {
			$keyword->relevance = $keyword->confidence;
			$keyword->text      = $keyword->name;
			unset($keyword->confidence, $keyword->name, $keyword->scheme);
		}
		return $array;
	}

	function setThreshold($threshold) {
		$this->threshold_limit = $threshold;	
		return $this;
	}

}

?>
<?php

class stackoverflowSearch {

	private $result;

	public function __construct() { 
		$this->result     = array();
		$this->cache      = new SimpleCache();
	}

	function build($url,$keywords,$categories) {
		if($URL_stack_decoded = $this->cache->get_cache('stack_search-'.sha1($url))){
			$this->result  = json_decode($URL_stack_decoded);
		} else {
			$topKeywords = utility::get_values_for_keys(array_slice($keywords, 0,2),"text");
			$stackSite   = $this->questionSiteMapper($categories);
			if ( $stackSite && count($stackSite) > 0 ) { 
				foreach ($topKeywords as $keyword) {
					$this->result = array_merge($this->result,$this->searchStackoverflow($keyword,$stackSite));
				}
			}
		}
		return $this->result;
	}

	function searchStackoverflow($keyword,$stackSite) {
		$result = array();
		if($stackoverflow_search_decoded = $this->cache->get_cache('stackoverflow-'.$keyword)){
			$result = json_decode($stackoverflow_search_decoded);
		} else {
			$stackoverflow_search = array();
			foreach($stackSite as $key=>$value) {
				$url = "select * from json where url='https://api.stackexchange.com/2.1/search/advanced?order=desc&pagesize=5&sort=votes&title=". urlencode($keyword) . "&site=". $value ."'";
				$stackoverflow_search = utility::getYQLResult($url);
				if (isset($stackoverflow_search->json->items)) {
					if (is_array($stackoverflow_search->json->items)) {
						foreach($stackoverflow_search->json->items as $entry) {
							$result[] =  $entry;
						}     
					} else $result[] =  $stackoverflow_search->json->items;
				} 
				$this->cache->set_cache('stackoverflow-'.$keyword, json_encode($result));
			}
		}
		return $this->parseStackoverflow($result);
	}

	function parseStackoverflow($result) {
		$results = array();
			foreach ($result as $question) {
				$args = array(
					"service"   => "stackoverflow",
					"type"      => "question",
					"time"      => utility::timeAgo(Date($question->creation_date)),
					"title"     => $question->title,
					"link"      => $question->question_id,
					);
				$results[] = $args; 
			}
		return $results;
	}

	function questionSiteMapper($category) {
		$result = array();
		$json   = file_get_contents('stack-mapper.json', true);
		$mappings = json_decode($json)->data;
		foreach ($mappings as $value) {
			$alchemyCat = is_array($category) ? $category['alchemy']: $category->alchemy;
			$zemantaCat = is_array($category) && !empty($category['zemanta']) ? $category['zemanta']: $category->zemanta;
			if (isset($value->alchemyCode) && $value->alchemyCode == $alchemyCat) {
				if (isset($value->DMOZ) && count(array_intersect($zemantaCat,$value->DMOZ)) > 0) $result = $value->stack;
			}
		}
		if (!empty($result)) return $result; else return false;
	}
}

?>
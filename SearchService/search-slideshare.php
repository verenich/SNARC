<?php

class slideshareSearch {

	private $result;

	public function __construct() { 
		$this->result     = array();
		$this->cache      = new SimpleCache();
	}

	function build($url,$keywords) {
		if($URL_slideshare_decoded = $this->cache->get_cache('slideshare_search-'.sha1($url))){
			$this->result  = json_decode($URL_slideshare_decoded);
		} else {
			$topKeyword = utility::get_values_for_keys(array_slice($keywords, 0,1),"text");
			$this->result  = $this->searchSlideshare($topKeyword[0]);
		}
		return $this->result;
	}

	function searchSlideshare($keyword) {
		$result = array();
		if($slideshare_search_decoded = $this->cache->get_cache('slideshare-'.$keyword)){
			$slideshare_search = json_decode($slideshare_search_decoded);
		} else {
			$time_stamp = strtotime("now");
			$hash = sha1("4xQZJ8CF". $time_stamp);
			$slideshare_query = "select * from xml where url='https://slideshare.net/api/2/search_slideshows?q=".urlencode($keyword)."&page=1&items_per_page=5&sort=relevance&upload_date=year&fileformat=all&lang=en&file_type=all&api_key=IO7frmfl&hash=" . $hash . "&ts=" . $time_stamp ."'";
			$slideshare_search = utility::getYQLResult($slideshare_query);
			$this->cache->set_cache('slideshare-'.$keyword, json_encode($slideshare_search));
		}
		if (isset($slideshare_search->Slideshows->Slideshow)) 
			$result[] =  $slideshare_search->Slideshows;
		return $this->parseSlideshare($result);
	}

	function parseSlideshare($result) {	
		$results = array();
		if (isset($result) && !empty($result[0]->Slideshow)) {
			foreach ($result[0]->Slideshow as $slide) {
				$args = array(
					"service"     => "slideshare",
					"type"        => "slide",
					"time"        => utility::timeAgo(strtotime($slide->Updated)),
					"title"       => $slide->Title,
					"description" => isset($slide->Description) ? $slide->Description : "",
					"link"        => isset($slide->URL) ? $slide->URL : "",
					"author"      => $slide->Username,
					"thumbnail"   => $slide->ThumbnailURL
					);
				$results[] = $args;
			}
		}
		return $results;
	}
}

?>
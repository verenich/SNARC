<?php


class vimeoSearch {

	private $result;
	private $vimeo_key;
	private $vimeo_secret;

	public function __construct() { 
		$this->result     = array();
		$this->cache      = new SimpleCache();
		$this->vimeo_key    = "8d21ef0f9c3693e2dcad5a4e4847554b9cc4d288";
		$this->vimeo_secret = "58642e824aed3b2f3da67db7c79c48cb2b0d2bc3";
	}

	function build($url,$keywords) {
		if($URL_vimeo_decoded = $this->cache->get_cache('vimeo_search-'.sha1($url))){
			$this->result  = json_decode($URL_vimeo_decoded);
		} else {
			$topKeyword = utility::get_values_for_keys(array_slice($keywords, 0,1),"text");	
			$this->result     = $this->searchVimeo(preg_replace("/[^a-zA-Z]+/", "", $topKeyword[0]));
		}
		return $this->result;
	}
	function searchVimeo($keyword) {
		$result = array();
		if($response_decoded = $this->cache->get_cache('vimeo-'.$keyword)){
			$vimeo_array = json_decode($response_decoded);
			$result[] =  $vimeo_array;
		} else {
			$vimeo_array = array();
			$vimeo = new phpVimeo($this->vimeo_key, $this->vimeo_secret);
			$response = $vimeo->call('vimeo.videos.search', array('per_page' => 7 , 'query' => $keyword, 'sort' => 'relevant'));
			foreach($response->videos->video as $v){
				$videoinfo = $vimeo->call('vimeo.videos.getInfo', array('video_id' => $v->id));
				$vimeo_array[] = $videoinfo->video[0];
			}
			$this->cache->set_cache('vimeo-'.$keyword, json_encode($vimeo_array));
			$result[] =  $vimeo_array;
		}  
		return $this->parseVimeo($result);
	}

	function parseVimeo($result) {
		$results = array();
		$oembed_endpoint = 'http://vimeo.com/api/oembed.xml';
		if (isset($result) && !empty($result)) {
			foreach ($result[0] as $video) {
				$args = array(
					"service"   => "vimeo",
					"type"      => "video",
					"title"     => $video->title,
					"time"      => utility::timeAgo(strtotime($video->modified_date)),
					"link"      => $video->urls->url[0]->_content,
					"embed"     => $video->urls->url[0]->_content,
					"author"    => $video->owner->display_name,
					"thumbnail" => $video->thumbnails->thumbnail[2]->{'_content'},
					"like"      => $video->number_of_likes
					);
				$results[] = $args; 
			}
		}
		return $results;
	}
}

?>
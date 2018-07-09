<?php

require('youTubeMapper.php');

class youTubeSearch {

	private $result;
	private $client;
	private $google_key;

	public function __construct($googleClient, $google_key) { 
		$this->result     = array();
		$this->client     = $googleClient;
		$this->google_key = $google_key;
		$this->cache      = new SimpleCache();
	}

	function build($url,$keywords,$category) {
		if($URL_youtube_decoded = $this->cache->get_cache('youtube_search-'.sha1($url))){
			$this->result  = json_decode($URL_youtube_decoded);
		} else {
			$youTubeMapper   = new youTubeMapper();
			$semanticKeyword = utility::get_values_for_keys(array_slice($keywords, 0,1),"text");
			$topKeywords     = utility::get_values_for_keys(array_slice($keywords, 0,2),"text");
			$joinedKeywords  = implode(' AND ', $topKeywords );

			$semanticYouTube = $this->youtube_semantic_search($semanticKeyword[0]);
			$youTubeCategory = $youTubeMapper->map($category);
			$youTubeSearch   = $youTubeCategory == "*" ? $this->youtube_search($semanticKeyword[0],$youTubeCategory) : $this->youtube_search($joinedKeywords,implode("+",$youTubeCategory));
			$this->result = array_merge($youTubeSearch,$semanticYouTube);
		}
		return $this->result;
	}

	function youtube_search($keyword, $category) {
		$result = array();
		if($youtube_search_decoded = $this->cache->get_cache('youtube_search-'.$keyword)){
			$youtube_search = json_decode($youtube_search_decoded);
		} else {
			$url = "select * from json where url='http://gdata.youtube.com/feeds/api/videos?q=".urlencode($keyword)."&alt=json&max-results=10&?category=". $category ."&key=". $this->google_key ."'";
			$youtube_search = utility::getYQLResult($url);
			$this->cache->set_cache('youtube_search-'.$keyword, json_encode($youtube_search));
		}
		if ( isset($youtube_search->json->feed->entry) ) {
			if ( is_array($youtube_search->json->feed->entry) ) {
				foreach ($youtube_search->json->feed->entry as $video) {
					$result[] =  $video;
				}
			} else  $result[] =  $youtube_search->json->feed->entry;  
		} 
		return $this->youTubeParser($result);
	}

	function youtube_semantic_search($keyword) {
		$result = array();
		if($searchResponse_semantic_decoded = $this->cache->get_cache('semantic_youtube-'.$keyword)){
			$searchResponse_semantic = json_decode($searchResponse_semantic_decoded);
		} 
		else { 
			$youtube = new Google_YoutubeService($this->client);
			$concepts = $this->getFreebaseConcept($keyword);
			$concept_depth = count($concepts) > 3 ? 3 : count($concepts); $counter = 0;
			while ($counter < $concept_depth && !isset($searchResponse_semantic) && $concepts ) {
				try {
					$searchResponse_semantic = $youtube->search->listSearch('snippet', array(
						'topicId'    => is_array($concepts->result) ? $concepts->result[$counter++]->mid : $concepts->result->mid  ,
						'order'      => 'viewCount',
						'maxResults' => 5
						));
					$this->cache->set_cache('semantic_youtube-'.$keyword, json_encode($searchResponse_semantic));
				} catch (Google_ServiceException $exception) {}
			}
		}
		if (isset($searchResponse_semantic)) {  
			foreach ($searchResponse_semantic->items as $video) {
				$result[] =  $video;
			}
		}
		return $this->semnticYouTubeParser($result);
	}

	function getFreebaseConcept($keyword) {
		$url = "select * from json where url=' https://www.googleapis.com/freebase/v1/search?query=".urlencode($keyword)."'";
		$freebase_result = utility::getYQLResult($url);
		if (isset($freebase_result->json->result)) 
			return $freebase_result->json;
	}

	function semnticYouTubeParser($result) {
		$results = array();
		if (isset($result) && !empty($result)) {
			foreach ($result as $video) {	
				$args = array(
					"service"   => "youtube",
					"type"      => "video",
					"time"      => utility::timeAgo(strtotime($video->snippet->publishedAt)),
					"title"     => $video->snippet->title,
					"author"    => $video->snippet->channelTitle,
					"embed"     => $video->id->videoId,
					"thumbnail" => is_array($video->snippet->thumbnails) ? $video->snippet->thumbnails["high"]->url : $video->snippet->thumbnails->high->url,
					"link"      => "http://www.youtube.com/watch?v=".$video->id->videoId
					);
				$results[] = $args; 
			}	
		}
		return $results;
	}

	function youTubeParser($result) {
		$results = array();
		if (isset($result) && !empty($result)) {
			foreach ($result as $video) {	
				$args = array(
					"service" => "youtube",
					"type"    => "video",
					"time"    => isset($video->published->_t) ? utility::timeAgo(strtotime($video->published->_t)) : utility::timeAgo(strtotime($video->published)),
					"title"   => isset($video->title->_t) ? $video->title->_t : $video->title,
					"link"    => str_replace("&feature=youtube_gdata", "", $video->link[0]->href),
					"author"  => isset($video->author->name->_t) ? $video->author->name->_t  : $video->group->credit->display,
					"embed"   => utility::parse_youtube_url($video->link[0]->href),
					"thumbnail" => $video->media_group->media_thumbnail[0]->url
					);
				$results[] = $args; 
			}	
		}
		return $results;
	}
}

?>
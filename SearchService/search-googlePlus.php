<?php

class googlePlusSearch {

	private $result;
	private $client;

	public function __construct($client) { 
		$this->result     = array();
		$this->cache      = new SimpleCache();
		$this->client     = $client;
	}

	function build($url,$keywords,$categories) {
		if($URL_google_decoded = $this->cache->get_cache('google_search-'.sha1($url))){
			$this->result  = json_decode($URL_google_decoded);
		} else {
			$semanticKeyword = utility::get_values_for_keys(array_slice($keywords, 0,1),"text");
			$topKeywords     = utility::get_values_for_keys(array_slice($keywords, 0,2),"text");
			$joinedKeywords  = implode('+', $topKeywords );
			$this->result = $this->searchGooglePlus($joinedKeywords);
			if (count($this->result) < 3 ) array_merge($this->result, $this->searchGooglePlus($semanticKeyword[0]));
		}
		return $this->result;
	}

	function searchGooglePlus($keyword) {
		if($searchResponse_decoded = $this->cache->get_cache('google_plus-'.$keyword)){
			$searchResponse = json_decode($searchResponse_decoded);
		} else {
			$plus = new Google_PlusService($this->client);
			$searchResponse = $plus->activities->search($keyword); 
			$this->cache->set_cache('google_plus-'.$keyword, json_encode($searchResponse));
		}  
		return $this->parseGooglePlus($searchResponse);
	}

	function parseGooglePlus($result) {
		$results = array();
		$author = $thumbnail =  $image = "";
		foreach ($result->items as $entry) {
			if ($entry->object->objectType != "image") {
				$time = utility::timeAgo(strtotime($entry->published));
				$item      = $entry->object;

				$thumbnail = $entry->actor->image->url;
				$author    = $entry->actor->displayName;
				$url       = $entry->url;
				$profile   = $entry->actor->url;
				if (isset($entry->title)) $title = $entry->title;

				if (isset($item->attachments)) {				
					$title                                                  = $item->attachments[0]->displayName;
					if (isset($item->attachments[0]->fullImage)) $image     = $item->attachments[0]->fullImage->url;
				} 
				if (!$title) $title = $item->content;
				$args = array(
					"service"   => "google",
					"type"      => "micropost",
					"time"      => $time,
					"title"     => substr($title, 0, 60),
					"link"      => $url,
					"profile"	=> $profile,
					"author"    => $author,
					"thumbnail" => $thumbnail
					);
				$results[] = $args; 
			}
		}
		return $results;
	}
}

?>
<?php

class twitterSearch {

	private $result;
	private $client;
	private $twitter;

	public function __construct() { 
		$this->result     = array();
		$this->cache      = new SimpleCache();
		$this->twitter    = Codebird::getInstance();
		Codebird::setConsumerKey('ECX9Nfjs12U2CQHGq0dHqw', 'oXju7ytKu5KW1HDW3bOWvjAIIGuO61Oi7BFdVFNais'); 
		$this->twitter->setToken('88829451-8sbKOhSzzQ3Yf2qSA281kxNeiV9pD14XdILblzKfI', 'jN99xiPsYltk9G3wuBSEp3vVSuZOdDOUSa3Orm8A');
	}

	function build($url,$keywords,$categories) {
		if($URL_twitter_decoded = $this->cache->get_cache('twitter_search-'.sha1($url))){
			$this->result  = json_decode($URL_twitter_decoded);
		} else {
			$semanticKeyword = utility::get_values_for_keys(array_slice($keywords, 0,1),"text");
			$topKeywords     = utility::get_values_for_keys(array_slice($keywords, 0,2),"text");
			$joinedKeywords  = implode(' ', $topKeywords );
			$ORedKeywords    = implode(' OR ', $topKeywords );
			$this->result = $this->searchTwitter($joinedKeywords);
			if (count($this->result) < 3 )
				$this->result = array_merge($this->result, $this->searchTwitter($ORedKeywords));
		}
		return $this->result;
	}

	function searchTwitter($keyword) {
		$result = array();
		if($twitter_search_decoded = $this->cache->get_cache('twitter_search-'.$keyword)){
			$twitter_search = json_decode($twitter_search_decoded);
		} else {
			$params = array ("lang" => "en","include_entities" => true,"q" => urlencode($keyword), "rpp" => 15 );
			$twitter_search = (array) $this->twitter->search_tweets($params);
			$this->cache->set_cache('twitter_search-'.$keyword, json_encode($twitter_search));
		}
		if ($twitter_search) {
			$result[] = is_array($twitter_search) ? $twitter_search["statuses"] : $twitter_search->statuses;
		}
		return $this->parseTwitter($result);
	}

	function parseTwitter($result) {
		$results = array();
		foreach ($result[0] as $tweet) {
			if ($tweet && isset($tweet->id)) {
				$args = array(
					"service"   => "twitter",
					"type"      => "micropost",
					"time"      => utility::timeAgo(strtotime($tweet->created_at)),
					"title"     => utility::makeUrls($tweet->text),
					"link"      => $tweet->id_str,
					"author"    => isset($tweet->user->screen_name) ? $tweet->user->screen_name : $tweet->user->name,
					"thumbnail" => $tweet->user->profile_image_url,
					"program" 	=> $tweet->source
					);
				$results[] = $args; 
			}
		}
		return $results;
	}
}

?>
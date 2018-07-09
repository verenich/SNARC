<?php

require_once ('APIs/google/Google_Client.php');
require_once ('APIs/google/Google_YouTubeService.php');
require_once ('APIs/google/contrib/Google_PlusService.php');
require_once ('APIs/twitter-oauth.php');
require_once ('APIs/vimeo-search.php');

require_once ('DocumentSearch/documentSearch.php');
require_once ('VideoSearch/videoSearch.php');
require_once ('search-slideshare.php');
require_once ('search-stackoverflow.php');
require_once ('search-googlePlus.php');
require_once ('search-twitter.php');

class SNARCSearch {

	private $result;
	private $cache;
	private $google_key;
	private $client;

	public function __construct() { 
		$this->result     = array();
		$this->cache      = new SimpleCache();
		$this->client     = new Google_Client();
		$this->google_key = "AIzaSyCa58KRY6XmjxmMsiSBMXLKxsSBC3_Yf40";
		$this->client->setDeveloperKey($this->google_key);
	}

	function build($document) {
		$documentSearch       = new documentSearch();
		$videoSearch          = new videoSearch($this->client,$this->google_key);
		$slideshareSearch     = new slideshareSearch();
		$stackoverflowSearch  = new stackoverflowSearch();
		$googlePlusSearch     = new googlePlusSearch($this->client);
		$twitterSearch        = new twitterSearch();

		$documentSearchResult = $documentSearch->build($document->url, $document->zemantaPosts);
		$videoSearchResult    = $videoSearch->build($document->url,$document->keywords, $document->categories);
		$slideshareResult     = $slideshareSearch->build($document->url,$document->keywords);
		$stackoverflowResult  = $stackoverflowSearch->build($document->url,$document->keywords,$document->categories);
		$googlePlusResult     = $googlePlusSearch->build($document->url,$document->keywords,$document->categories);
		$twitterSearchResult  = $twitterSearch->build($document->url,$document->keywords,$document->categories);

		return array_merge($documentSearchResult,$videoSearchResult,$slideshareResult,$stackoverflowResult,$googlePlusResult,$twitterSearchResult);
	}
}

?>
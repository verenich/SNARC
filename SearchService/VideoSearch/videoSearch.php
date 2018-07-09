<?php

require_once ('youTubeSearch.php');
require_once ('vimeoSearch.php');

class videoSearch {

	private $result;
	private $vimeo_key;
	private $vimeo_secret;
	private $google_key;
	private $client;

	public function __construct($client,$google_key) { 
		$this->result     = array();
		$this->cache      = new SimpleCache();
		$this->google_key = $google_key;
		$this->client     = $client;
	}

	function build($url,$keywords,$categories) {
		$youTubeSearch  = new youTubeSearch($this->client,$this->google_key);
		$vimeoSearch    = new vimeoSearch();

		$youTuberesult	= $youTubeSearch->build($url,$keywords, $categories);
		$vimeoResult	= $vimeoSearch->build($url,$keywords);

		return array_merge($youTuberesult,$vimeoResult);
	}
}

?>
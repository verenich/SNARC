<?php

require_once ('google-search.php');
require_once ('zemantaParser.php');
require_once ('shareCalculator.php');

class documentSearch {

	private $result;

	public function __construct() { 
		$this->result = array();
	}

	function build($url,$zemantaPosts) {
		$googleSearch  = new googleSearch();
		$zemantaParser = new zemantaParser();	

		$this->result   = array_merge($this->result,$googleSearch->build(urlencode($url)));	
		$this->result   = array_merge($this->result,$zemantaParser->build($zemantaPosts));

		return $this->result;
	}
}

?>
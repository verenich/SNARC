<?php

class zemantaParser {

	private $result;

	public function __construct() { 
		$this->result = array();
		$this->shareCalculator = new shareCalculator();	
	}

	function build($zemantaPosts) {
		foreach ($zemantaPosts as $entry) {
			$args = array(
				"service"   => "zemanta",
				"type"      => "post",
				"time"      => utility::timeAgo(strtotime($entry->published_datetime)),
				"title"     => $entry->title,
				"link"      => $entry->url,
				"content"   => $entry->text_preview,
				"share"     => $this->shareCalculator->build($entry->url)
				);
			$this->result[] =  $args; 
		}
		return $this->result;
	}
}

?>
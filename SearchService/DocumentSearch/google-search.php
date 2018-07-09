<?php

require_once("./APIs/qpath/src/qp.php");
require_once('./APIs/readability/readability.php');

class googleSearch {

	private $result;

	public function __construct()
	{
		$this->cache = new SimpleCache();	
	}

	function build($query) {
		if($google_decoded = $this->cache->get_cache('google:'.sha1($query))){
			$this->result = $google_decoded;
		} else {
			$Googleresults = array();
			$CURLurl         = "http://www.google.com/search?q=".$query."&ie=UTF-8";
			$googleQueryPath = htmlqp($CURLurl);
			$results         = $googleQueryPath->find(".g");

			if ($results && count($results) > 0 ) {
				foreach($results as $result) {
					$shareCalculator         = new shareCalculator();
					$extractedLink           = $result->find('h3.r a')->attr('href');
					$extractedLink_semiClean = explode('&sa=',$extractedLink,2);
					$extractedLink_cleaned   = str_replace('/url?q=','',$extractedLink_semiClean[0]);
					$title                   = $result->find('h3.r')->text();
					$time                    = explode('...',$result->find('.st')->text(),2);
					$excerpt                 = true;
					if ($excerpt && similar_text(urldecode($query), $extractedLink_cleaned) < 0.9 ) {
						$googleResult = array(
							"type"    => "post",
							"service" => "GoogleSearch",
							"time"    => "Sometime in the past ...",
							"title"   => htmlentities($title),
							"link"    => $extractedLink_cleaned,
							"share"   => $shareCalculator->build($extractedLink_cleaned),
							);
						$Googleresults[] = $googleResult;
					}
				}
			}
			$this->cache->set_cache('google:'.sha1($query) , json_encode($Googleresults));
			$this->result = json_encode($Googleresults);
		}
		return json_decode($this->result);
	}

	function getExcerpt($url) {
		$html = utility::getCURLResult($url);
		if ($html) { 
			if (function_exists('tidy_parse_string')) {
				$tidy = tidy_parse_string($html, array());
				$tidy->cleanRepair();
				$html = $tidy->value;
			}
			$readability = new Readability($html);
			$readability->debug = false;
			$readability->convertLinksToFootnotes = false;
			$result = $readability->init();
			if ($result) {
				$innerHTML = $readability->getContent()->innerHTML;
				$text      = qp($innerHTML)->find('p:first')->text();
				if (strlen($text) > 10 ) return $text; else return false;
			}
		} 
		return false;
	}
}

?>
<?php

require_once('APIs/readability/readability.php');

class parser {

	private $document;

	public function __construct()
	{
		$this->document = array();
		$this->cache    = new SimpleCache();	
	}

	function build($url) {
		if($document_decoded = $this->cache->get_cache(sha1($url))){
			$this->document = json_decode($document_decoded);
		} else {
			$html = utility::getCURLResult($url);
			// PHP Readability works with UTF-8 encoded content
			// If $html is not UTF-8 encoded, use iconv() or mb_convert_encoding() to convert to UTF-8.

			// If we've got Tidy, let's clean up input
			//This step is highly recommended - PHP's default HTML parser often does a terrible job and results in strange output.
			if (function_exists('tidy_parse_string')) {
				$tidy = tidy_parse_string($html, array());
				$tidy->cleanRepair();
				$html = $tidy->value;
			}

			//Basic Initiation and parameters setting
			$readability = new Readability($html);
			$readability->debug = false;
			$readability->convertLinksToFootnotes = false;

			$result = $readability->init();
			
			if ($result) {
				$this->document["url"]   = $url;
				$this->document["title"] = $readability->getTitle()->textContent;
				$this->document["text"]  = $readability->getContent()->textContent;

				$document_encoded        = json_encode($this->document);
				$this->document          = json_decode($document_encoded);
				$this->cache->set_cache(sha1($url) , $document_encoded);
			} else {
				return false;
			}
		}
		return $this->document;
	}
}

?>
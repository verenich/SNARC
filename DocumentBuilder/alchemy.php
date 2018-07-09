<?php

class alchemy {

	public function __construct()
	{
		$this->alchemy_key = "2479fca3a681eff212131061a5c3bdcebab1c55a";
		$this->url         = "http://access.alchemyapi.com/calls/url/";
		$this->cache       = new SimpleCache();
	}

	function call($document, $function_name ) {
		$result = '';
		if ($encoded_result = $this->cache->get_cache($function_name.':'.sha1($document->getURL()))){
			$result = json_decode($encoded_result);
		} else {
			$result = call_user_func_array(array($this, $function_name), array($document));
			$this->cache->set_cache($function_name.':'.sha1($document->getURL()), json_encode($result));
		}
		return $result;
	}

	function setURL($url) {
		$this->url = $url; return;
	}

	function extractText($document) {
		$curl_url       = $this->url.'URLGetText?outputMode=json&apikey='.$this->alchemy_key.'&url='.urlencode($document->getURL()); 		
		$extracted_text = json_decode(utility::getCURLResult($curl_url));
		$text           = isset($extracted_text->text) ? $extracted_text->text : "";
		return $text;
	}

	function extractFullText($document) {
		$curl_url       = $this->url.'URLGetRawText?outputMode=json&apikey='.$this->alchemy_key.'&url='.urlencode($document->getURL()); 		
		$extracted_text = json_decode(utility::getCURLResult($curl_url));
		$text           = isset($extracted_text->text) ? $extracted_text->text : "";
		return $text;
	}

	function extractTitle($document) {
		$curl_url        = $this->url.'URLGetTitle?outputMode=json&apikey='.$this->alchemy_key.'&url='.urlencode($document->getURL()); 		
		$extracted_title = json_decode(utility::getCURLResult($curl_url));
		$title           = isset($extracted_title->title) ? $extracted_title->title : "";
		return $title;
	}

	function extractLanguage($document) {
		$curl_url       = $this->url.'URLGetLanguage?outputMode=json&apikey='.$this->alchemy_key.'&url='.urlencode($document->getURL()); 		
		$extracted_lang = json_decode(utility::getCURLResult($curl_url));
		$lang           = isset($extracted_lang->{'iso-639-1'}) ? $extracted_lang->{'iso-639-1'} : "";
		$fullLanguge    = isset($extracted_lang->language) ? $extracted_lang->language : "";
		return json_decode(json_encode(array("code"=> $lang , "language" => $fullLanguge)));
	}

	function extractCategory($document) {
		$curl_url      = $this->url.'URLGetCategory?outputMode=json&apikey='.$this->alchemy_key.'&url='.urlencode($document->getURL()); 		
		$extracted_cat = json_decode(utility::getCURLResult($curl_url));
		$cat           = isset($extracted_cat->category) ? $extracted_cat->category : array();
		return $cat;
	}

	function extractKeywords($document) {
		$curl_url           = $this->url.'TextGetRankedKeywords?outputMode=json&sentiment=0&keywordExtractMode=strict&maxRetrieve=10&apikey='.$this->alchemy_key.'&text='.urlencode($document->getText()); 		
		$extracted_keywords = json_decode(utility::getCURLResult($curl_url));
		$keywords           = isset($extracted_keywords->keywords) ? $extracted_keywords->keywords : array();
		return $keywords;
	}

	function extractEntities($document) {
		$curl_url           = $this->url.'TextGetRankedNamedEntities?outputMode=json&sentiment=0&apikey='.$this->alchemy_key.'&text='.urlencode($document->getText()); 		
		$extracted_entities = json_decode(utility::getCURLResult($curl_url));
		$entities           = isset($extracted_entities->entities) ? $extracted_entities->entities : array();
		return $entities;
	}

	function extractConcepts($document) {
		$curl_url           = $this->url.'TextGetRankedConcepts?outputMode=json&apikey='.$this->alchemy_key.'&text='.urlencode($document->getText()); 		
		$extracted_concepts = json_decode(utility::getCURLResult($curl_url));
		$concepts           = isset($extracted_concepts->concepts) ? $extracted_concepts->concepts : array();
		return $concepts;
	}

	function extractKeywordsURL($document) {
		$curl_url           = $this->url.'URLGetRankedKeywords?outputMode=json&sentiment=0&keywordExtractMode=strict&maxRetrieve=10&apikey='.$this->alchemy_key.'&url='.urlencode($document->getURL()); 		
		$extracted_keywords = json_decode(utility::getCURLResult($curl_url));
		$keywords           = isset($extracted_keywords->keywords) ? $extracted_keywords->keywords : array();
		return $keywords;
	}

	function extractEntitiesURL($document) {
		$curl_url           = $this->url.'URLGetRankedNamedEntities?outputMode=json&sentiment=0&apikey='.$this->alchemy_key.'&url='.urlencode($document->getURL()); 		
		$extracted_entities = json_decode(utility::getCURLResult($curl_url));
		$entities           = isset($extracted_entities->entities) ? $extracted_entities->entities : array();
		return $entities;
	}

	function extractConceptsURL($document) {
		$curl_url           = $this->url.'URLGetRankedConcepts?outputMode=json&apikey='.$this->alchemy_key.'&url='.urlencode($document->getURL()); 		
		$extracted_concepts = json_decode(utility::getCURLResult($curl_url));
		$concepts           = isset($extracted_concepts->concepts) ? $extracted_concepts->concepts : array();
		return $concepts;
	}
}

?>
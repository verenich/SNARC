<?php

class semanticDocument {

	public $url;
	public $title;
	public $text;
	public $language;
	public $languageCode;
	public $categories;
	public $keywords;
	public $concepts;
	public $entities;
	public $zemantaPosts;

	public function __construct()
	{
		$this->title              = '';
		$this->text               = '';
		$this->url                = '';
		$this->language           = '';
		$this->languageCode       = '';
		$this->categories         = array();
		$this->keywords           = array();			
		$this->concepts           = array();
		$this->entities           = array();
		$this->zemantaPosts       = array();
	}

	public function getURL() {
		return $this->url;
	}
	
	public function setURL($url) {
		$this->url = $url;	
		return $this;
	}

	public function getZemantaPosts() {
		return $this->zemantaPosts;
	}
	
	public function setZemantaPosts($zemantaPosts) {
		$this->zemantaPosts = $zemantaPosts;	
		return $this;
	}

	public function getTitle() {
		return $this->title;
	}
	
	public function setTitle($title) {
		$this->title = $title;	
		return $this;
	}

	public function getText() {
		return $this->text;
	}
	
	public function setText($text) {
		$this->text = $text;	
		return $this;
	}

	public function getLanguageCode() {
		return $this->languageCode;
	}
	
	public function setLanguageCode($languageCode) {
		$this->languageCode = $languageCode;	
		return $this;
	}

	public function getLanguage() {
		return $this->language;
	}
	
	public function setLanguage($language) {
		$this->language = $language;	
		return $this;
	}

	public function getCategories() {
		return $this->categories;
	}
	
	public function setCategories($categories) {
		$this->categories = $categories;	
		return $this;
	}

	public function getEntities() {
		return $this->entities;
	}
	
	public function setEntities($entities) {
		$this->entities = $entities;	
		return $this;
	}

	public function getKeywords() {
		return $this->keywords;
	}
	
	public function setKeywords($keywords) {
		$this->keywords = $keywords;	
		return $this;
	}

	public function getConcepts() {
		return $this->concepts;
	}
	
	public function setConcepts($concepts) {
		$this->concepts = $concepts;	
		return $this;
	}
}

?>
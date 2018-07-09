<?php

class zemanta {

	private $zemanta;

	public function __construct()
	{
		$this->zemanta_key = "xdjapswuumufqgjob52swtln";
		$this->cache       = new SimpleCache();	
	}

	function build($id,$title,$text) {
		if($zemanta_decoded = $this->cache->get_cache('zemanta:'.$id)){
			$zemanta = json_decode($zemanta_decoded);
		} else {
			$url  = 'http://api.zemanta.com/services/rest/0.0/'; $data = "";
			$args = array(
				'method'            => "zemanta.suggest",
				'api_key'           => $this->zemanta_key,
				'text'              => $text,
				'format'            => 'json',
				'return_rdf_links'  => 1,
				'return_keywords'   => 1,
				'return_categories' => "dmoz",
				'return_images'     => 0,
				'text_title'        => $title);

			foreach($args as $key=>$value)
				{ $data .= ($data != "")?"&":"";
			$data .= urlencode($key)."=".urlencode($value);}

			$zemanta = utility::getCURLPostResult($url,$data);
			$this->cache->set_cache('zemanta:'.$id , json_encode($zemanta));
		}
		$this->zemanta = $zemanta;
	}

	function getKeywords() {
		if (isset($this->zemanta->keywords) && count($this->zemanta->keywords) > 0 ) {
			return $this->zemanta->keywords;
		} else return array();
	}

	function getRelatedPosts() {
		if (isset($this->zemanta->articles) && count($this->zemanta->articles) > 0 ) {
			return $this->zemanta->articles;
		} else return array();
	}

	function getCategories() {
		if (isset($this->zemanta->categories) && count($this->zemanta->categories) > 0 ) {
			return $this->zemanta->categories;
		} else return array();
	}

	function getEntities() {
		if (isset($this->zemanta->markup->links) && count($this->zemanta->markup->links) > 0 ) {
			return $this->zemanta->markup->links;
		} else return array();
	}

	function getZemanta(){
		return $this->zemanta;
	}
}

?>
<?php

class youTubeMapper {

	public function __construct()
	{
		$this->json   = file_get_contents('http://ahmadassaf.com/projects/SNARC/SearchService/VideoSearch/alchemy_youtube_mapper.json', true);
		$this->result = array();	
	}

	function map($categories) {
		$result = array();
		$mappings = json_decode($this->json)->data;
		foreach ($mappings as $value ) {
			$alchemyCat = is_array($categories) ? $categories['alchemy']: $categories->alchemy;
			$zemantaCat = is_array($categories) && !empty($categories['zemanta']) ? $categories['zemanta']: $categories->zemanta;
			if (isset($value->alchemyCode) && $value->alchemyCode == $alchemyCat) {
				if (isset($value->DMOZ) && count(array_intersect($zemantaCat,$value->DMOZ)) > 0) $result = $value->youtube;
			}
		}
		if (!empty($result)) return $result; else return "*";
	}
}

?>
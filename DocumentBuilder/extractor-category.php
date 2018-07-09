<?php

class categoryExtractor {

	private $categories;

	public function __construct()
	{
		$this->categories  = array();
	}

	function getCategories($alchemy_categories, $zemanta_categories) {
		$zemanta_top_entities = array();
		if (isset($zemanta_categories) && count($zemanta_categories) > 0 ) {
			foreach($zemanta_categories as $category) {
				$explodedCategories = explode("/", $category->name);
				if (count($explodedCategories) > 1 ) $zemanta_top_entities[] = $explodedCategories[1];
			}
		}
		$this->categories["alchemy"] = $alchemy_categories;
		$this->categories["zemanta"] = array_values(array_filter(array_unique($zemanta_top_entities)));

		return $this->categories;
	}
}

?>
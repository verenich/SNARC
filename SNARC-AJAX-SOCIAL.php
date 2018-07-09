<?php 

	header('Content-type: application/json');
	date_default_timezone_set('UTC');

	require_once('Utilities/util.php');
	require_once('Utilities/simpleCache.php');
	require_once('SearchService/SNARC-search.php');

	$cache = new SimpleCache();

	$id               = "socialAggregator".sha1($_POST['url']);
	$semanticDocument = $_POST['d'];

	if($data = $cache->get_cache($id)){
		echo($data);
		die();
	} 
	else { 
		$SNARCSearch             = new SNARCSearch();
		$result                  = $SNARCSearch->build(json_decode($semanticDocument));

		$result = array_filter($result);
		shuffle($result);
		$encoded_result = json_encode($result);
		$cache->set_cache($id,$encoded_result );
		
		echo($encoded_result);
		die();
	}
?>

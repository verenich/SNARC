<?php 

	header('Content-type: application/json; charset=utf8');
	date_default_timezone_set('UTC');

	$pusher_app_id      = '44071';
	$pusher_app_key     = '897f81550cd6da4f4df3';
	$pusher_app_secret  = '0d3250dbb5b872fb9b6e';	

	require_once('Utilities/util.php');
	require_once('Utilities/simpleCache.php');

	require_once('DocumentBuilder/semanticDocumentBuilder.php');

	$semanticDocumentBuilder = new semanticDocumentBuilder();
	$result =  $semanticDocumentBuilder->build($_POST['url']);

	echo json_encode($result);
	die();

?>
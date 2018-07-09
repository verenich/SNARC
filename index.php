<?php

	require_once('Utilities/util.php');
	require_once('Utilities/simpleCache.php');

	require_once('DocumentBuilder/semanticDocumentBuilder.php');
	require_once('SearchService/SNARC-search.php');

	$semanticDocumentBuilder = new semanticDocumentBuilder();
	$SNARCSearch             = new SNARCSearch();

	$semanticDocument        = $semanticDocumentBuilder->build("http://www.bbc.com/news/world-middle-east-28290018");
	$result                  = $SNARCSearch->build($semanticDocument);

	utility::debug($result);

?>
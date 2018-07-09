<?php

require_once ('alchemy.php');
require_once ('zemanta.php');
require_once ('semantic-document.php');
require_once ('parser.php');
require_once ('extractor-keyword.php');
require_once ('extractor-entity.php');
require_once ('extractor-category.php');

class semanticDocumentBuilder {

	public function __construct()
	{
		$this->result             = array();
		$this->cache              = new SimpleCache();		
	}

	function build($url) {

		if ($document_encoded_result = $this->cache->get_cache('Semanticdocument:'.sha1($url))){
			$document = json_decode($document_encoded_result);
		} else {
			$document          = new semanticDocument();
			$documentParser    = new parser();
			$alchemyExtractor  = new alchemy();
			$zemantaExtractor  = new zemanta();
			$keywordExtractor  = new keywordExtractor(5, false, 0.50);
			$entityExtractor   = new entityExtractor(0.9);
			$categoryExtractor = new categoryExtractor();

			$document->setURL($url);
			//Fallback when CURL result is denied
			$parsed_document  =  $documentParser->build($url);
			
			$documentTitle = (!isset($parsed_document->title) || empty($parsed_document->title)) ? $alchemyExtractor->call($document, "extractTitle") : $parsed_document->title;
			$documentText  = (!isset($parsed_document->text)  || empty($parsed_document->text )) ? $alchemyExtractor->call($document, "extractText")  : $parsed_document->text;

			//Set the main proprties of the document
			$document->setTitle($documentTitle);
			$document->setText ($documentText);

			$alchemy_language = $alchemyExtractor->call($document, "extractLanguage");
			$alchemy_category = $alchemyExtractor->call($document, "extractCategory");

			//retreive the Alchemy API related proprties
			if (!empty($parsed_document->text)) $alchemyExtractor->setURL("http://access.alchemyapi.com/calls/text/");
			$alchemy_keywords = empty($parsed_document->text) ? $alchemyExtractor->call($document, "extractKeywordsURL") : $alchemyExtractor->call($document, "extractKeywords");
			$alchemy_concepts = empty($parsed_document->text) ? $alchemyExtractor->call($document, "extractConceptsURL") : $alchemyExtractor->call($document, "extractConcepts");
			$alchemy_entities = empty($parsed_document->text) ? $alchemyExtractor->call($document, "extractEntitiesURL") : $alchemyExtractor->call($document, "extractEntities");

			//retreive the related Zemanta information
			$zemantaExtractor->build(sha1($url),$document->getTitle(),$document->getText());

			$document->setLanguage($alchemy_language->language);
			$document->setLanguageCode($alchemy_language->code);
			$document->setZemantaPosts($zemantaExtractor->getRelatedPosts());
			$document->setKeywords($keywordExtractor->getKeywords($alchemy_keywords, $zemantaExtractor->getKeywords()));
			$document->setEntities($entityExtractor->getEntities($alchemy_entities, $zemantaExtractor->getEntities()));
			$document->setConcepts($alchemy_concepts);
			$document->setCategories($categoryExtractor->getCategories($alchemy_category ,$zemantaExtractor->getCategories()));		
		}

		$this->cache->set_cache('Semanticdocument:'.sha1($url), json_encode($document));
		return $document;

	}
}

?>
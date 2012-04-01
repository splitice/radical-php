<?php
namespace Net\ExternalInterfaces\Carrot2;

/**
 * Performs processing using Carrot2 Document Clustering Server. 
 */
class Processor {
	private $baseurl;
	private $format = 'JSON';
	
	/**
	 * Creates a Carrot2 processor.
	 *
	 * @param $baseurl Carrot2 DCS service url, defaults to 'http://localhost:8080/dcs/rest'
	 */
	public function __construct($baseurl = 'http://127.0.0.1:8080/dcs/rest') {
		$this->baseurl = $baseurl;
	}
	
	/**
	 * Processes the provided Carrot2 job.
	 *
	 * @return returns Result with processing results
	 * @throws Exception in case of unrecoverable errors, e.g. no connection to DCS
	 */
	public function cluster(Job $job) {
		$curl = curl_init ( $this->baseurl );
		
		// Prepare request parameters
		$fields = array_merge ( $job->getAttributes (), array ('dcs.output.format' => $this->format ) );
		
		$documents = $job->getDocuments ();
		
		if (count ( $documents ) > 0) {
			$fields ['dcs.c2stream'] = $this->generateXml ( $documents );
		}

		self::addIfNotNull ( $fields, 'dcs.source', $job->getSource () );
		self::addIfNotNull ( $fields, 'dcs.algorithm', $job->getAlgorithm () );
		self::addIfNotNull ( $fields, 'query', $job->getQuery () );

		// Make POST request
		curl_setopt_array ( $curl, array (CURLOPT_TIMEOUT=>3, CURLOPT_POST => true, CURLOPT_HTTPHEADER => array ('Content-Type: multipart/formdata' ), CURLOPT_HEADER => false, CURLOPT_RETURNTRANSFER => true, CURLOPT_POSTFIELDS => $fields ) );
		$response = curl_exec ( $curl );

		$error = curl_errno ( $curl );
		if ($error !== 0) {
			throw new Exception ( curl_error ( $curl ) );
		}
		$httpStatus = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );
		if ($httpStatus >= 400) {
			return null;
			//die ( var_dump ( $response ) );
			throw new Exception ( 'HTTP error occurred, error code: ' . $httpStatus );
		}
		
		return $this->extractResponse ( $response );
	}
	
	private static function FixUTF8($s) {
		return htmlspecialchars(@iconv ( "UTF-8", "UTF-8//IGNORE", $s ));
	}
	
	/**
	 * Generates XML with the directly provided documents.
	 */
	private function generateXml($documents) {
		$ret = '<?xml version="1.0" encoding="UTF-8"?>'."\r\n<searchresult>";
		foreach ( $documents as $document ) {
			$ret .= '<document id="'.$document->getId().'">';
			$title = $document->getTitle();
			if($title){
				$ret .= '<title>'.self::FixUTF8 ($title).'</title>';
			}
			$snippet = $document->getContent ();
			if($snippet){
				$ret .= '<snippet>'.self::FixUTF8 ($snippet).'</snippet>';
			}
			$ret .= '</document>';
		}
		$ret .= '</searchresult>';
		return $ret;
		/*$dom = new \DOMDocument ( '1.0', 'UTF-8' );
		$resultsElement = $dom->createElement ( 'searchresult' );
		$dom->appendChild ( $resultsElement );
		foreach ( $documents as $document ) {
			$documentElement = $dom->createElement ( 'document' );
			if ($document->getId () != null) {
				$documentElement->setAttribute('id', $document->getId());
			}
			$this->appendTextField ( $dom, $documentElement, 'title', self::FixUTF8 ( $document->getTitle () ) );
			$snippet = self::FixUTF8 ( $document->getContent () );
			if($snippet){
				$this->appendTextField ( $dom, $documentElement, 'snippet', $snippet );
			}
			if($document->getUrl ()){
				$this->appendTextField ( $dom, $documentElement, 'url', $document->getUrl () );
			}
			$resultsElement->appendChild ( $documentElement );
		}
		return $dom->saveXML ();*/
	}
	
	/*private function appendTextField($dom, $elem, $name, $value) {
		$text = $dom->createElement ( $name );
		$text->appendChild ( $dom->createTextNode ( ( string ) $value ) );
		$elem->appendChild ( $text );
	}*/
	
	/**
	 * Extracts Results from the XML response.
	 */
	private function extractResponse($rawXml) {
		if($this->format == 'JSON'){
			$json = json_decode($rawXml);
			$documents = array();
			if(isset($json->documents)){
				foreach($json->documents as $d){
					$documents[] = new Document ( $d->title, isset($d->snippet)?$d->snippet:'', '', array(), $d->id );
				}
			}
			$clusters = array();
			foreach($json->clusters as $c){
				$clusters[] = new Cluster ( implode(', ',$c->phrases), $c->score, $c->documents, array() );
			}
			return new Result ( $documents, $clusters, array(), $rawXml );
		}else{
			$xml = new \SimpleXMLElement ( $rawXml );
			return new Result ( $this->extractDocuments ( $xml ), $this->extractClusters ( $xml->xpath ( '/searchresult/group' ) ), $this->extractAttributes ( $xml->xpath ( '/searchresult/attribute' ) ), $rawXml );
		}
	}
	
	private function extractDocuments($xml) {
		$documents = array ();
		foreach ( $xml->xpath ( '/searchresult/document' ) as $documentElement ) {
			$document = new Document ( ( string ) $documentElement->title, ( string ) $documentElement->snippet, ( string ) $documentElement->url, array(), ( string ) $documentElement ['id'] );
			$documents [] = $document;
		}
		return $documents;
	}
	
	private function extractClusters($groupElements) {
		$clusters = array ();
		
		foreach ( $groupElements as $group ) {
			$documentIds = array ();
			foreach ( $group->xpath ( 'document' ) as $document ) {
				$documentIds [] = ( string ) $document ['refid'];
			}
			
			$subclusters = $this->extractClusters ( $group->xpath ( 'group' ) );
			
			$cluster = new Cluster ( ( string ) $group->title->phrase, ( string ) $group ['score'], $documentIds, $subclusters );
			$clusters [] = $cluster;
		}
		
		return $clusters;
	}
	
	private function extractAttributes($attributeElements) {
		$attributes = array ();
		foreach ( $attributeElements as $attribute ) {
			$key = $attribute ['key'];
			$valueElement = $attribute->xpath ( 'value' );
			if (count ( $valueElement ) > 0) {
				$value = $valueElement [0] ['value'];
				if ($value) {
					$attributes [( string ) $key] = ( string ) $value;
				}
			}
		}
		return $attributes;
	}
	
	private static function addIfNotNull(&$array, $key, $value) {
		if ($value) {
			$array [$key] = $value;
		}
	}
}
<?php
namespace Net\ExternalInterfaces\Carrot2;

/**
 * Represents the results of Carrot2 processing, contains document fetched from the 
 * external document source (if requested) and clusters.
 */
class Result {
	private $documents;
	private $clusters;
	private $attributes;
	private $xml;
	
	public function __construct($documents = array(), $clusters = array(), $attributes = array(), $xml = null) {
		$this->documents = $documents;
		$this->clusters = $clusters;
		$this->attributes = $attributes;
		$this->xml = $xml;
	}
	
	/**
	 * Returns the documents that have been clustered. Keys in this array correspond do
	 * document identifiers obtained, e.g. from Carrot2Cluster::getDocumentIds().
	 */
	public function getDocuments() {
		return $this->documents;
	}
	
	/**
	 * Returns the created clusters.
	 */
	public function getClusters() {
		return $this->clusters;
	}
	
	/**
	 * Returns an array of additional attributes set by the clustering engine.
	 * Keys in the returned array correspond to attribute keys, values to attribute values.
	 * For a list of supported attribute keys, please refer to Carrot2 Manual.
	 */
	public function getAttributes() {
		return $this->attributes;
	}
	
	/**
	 * Returns the raw XML response received from the DCS.
	 */
	public function getXml() {
		return $this->xml;
	}
}
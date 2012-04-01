<?php
namespace Net\ExternalInterfaces\Carrot2;

/**
 * Represents a Carrot2 document. Instances of this class are used both to provide documents
 * for clustering and access the documents retrieved from an external source, if requested.
 */
class Document {
	private $id;
	private $title;
	private $content;
	private $url;
	private $otherFields;
	
	public function __construct($title, $content = '', $url = '', array $otherFields = array(), $id = null) {
		$this->id = $id;
		$this->title = $title;
		$this->content = $content;
		$this->url = $url;
		$this->otherFields = $otherFields;
	}
	
	/**
	 * Returns a unique document identifier.
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Returns document's title.
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * Returns document's content.
	 */
	public function getContent() {
		return $this->content;
	}
	
	/**
	 * Returns document's url.
	 */
	public function getUrl() {
		return $this->url;
	}
	
	/**
	 * Returns value of a document fields, other than title, content and url.
	 *
	 * @param $fieldName name of the field
	 * @return value of the field or null
	 */
	public function getField($fieldName) {
		return isset ( $this->otherFields [$fieldName] ) ? $this->otherFields [$fieldName] : null;
	}
	
	/**
	 * Returns an array of other document's fields. Keys in the array correspond to field names,
	 * values to field values. Please refer to Carrot2 documentation for the fields supported by
	 * specific document sources.
	 */
	public function getOtherFields() {
		return $this->otherFields;
	}
}
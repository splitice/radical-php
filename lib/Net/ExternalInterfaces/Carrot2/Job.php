<?php
namespace Net\ExternalInterfaces\Carrot2;

/**
 * Contains data for Carrot2 processing. Two kinds of jobs can be submitted:
 *
 * - Clustering directly provided documents. For this job, add the documents
 * to be clustered using the addDocument() method. All other data items are optional.
 *
 * - Clustering documents fetched by Carrot2 from some data source. For this job,
 * set the query and document source. All other data items are optional.
 */
class Job {
	private $documents = array ();
	private $query;
	private $source;
	private $algorithm;
	private $attributes = array ();
	
	/**
	 * Adds a document for clustering. You can either provide documents using this 
	 * method or the document source to search using the setSource() method, but not
	 * both. 
	 *
	 * @param $title title of the document to cluster, required
	 * @param $content content of the document to cluster, optional
	 * @param $url url of the document to cluster, optional
	 */
	public function addDocument($title, $content = '', $url = '', $id = null) {
		$this->documents [] = new Document ( $title, $content, $url, array (), $id );
	}
	
	public function getDocuments() {
		return $this->documents;
	}
	
	public function setDocuments($array) {
		foreach($array as $a){
			$this->documents [] = new Document ( $a[0], $a[1], '', array (), $a[2] );
		}
	}
	
	/**
	 * Sets the source from which Carrot should fetch documents for clustering. You can 
	 * either set the document source using this method or directly add documents using
	 * addDocument but not both.
	 *
	 * @param $source identifier of the document source to query. Check the "Parameters"
	 * tab of the DCS welcome screen for the list of supported source identifiers.
	 */
	public function setSource($source) {
		$this->source = $source;
	}
	
	public function getSource() {
		return $this->source;
	}
	
	/**
	 * Sets the algorithm Carrot should use to cluster documents. Setting the algorithm
	 * is optional, if not set, the default algorithm will be used.
	 * 
	 * @param $algorithm identified of the clustering algorithm to use. Check the "Parameters"
	 * tab of the DCS welcome screen for the list of supported algorithm identifiers.
	 */
	public function setAlgorithm($algorithm) {
		$this->algorithm = $algorithm;
	}
	
	public function getAlgorithm() {
		return $this->algorithm;
	}
	
	/**
	 * Sets the query to execute or query hint. If you want to use Carrot to query some data
	 * source specified by setSource(), you must set the query using this method. If you
	 * provide documents directly using the addDocument() method and you know which query
	 * generated these documents, you can optionally set this query using this method, which
	 * will improve the clustering quality.
	 *
	 * @param $query query to execute when document source is set or query hint when
	 * providing documents directly
	 */
	public function setQuery($query) {
		$this->query = $query;
	}
	
	public function getQuery() {
		return $this->query;
	}
	
	/**
	 * Sets additional tuning attributes for Carrot2 document sources or algorithms. Please
	 * refer to Carrot2 manual for the list of supported attributes for each source and
	 * algorithm.
	 *
	 * @param $attributes an associative array with attribute keys as keys, and attribute
	 * values as values
	 */
	public function setAttributes(array $attributes) {
		if (is_array ( $attributes )) {
			$this->attributes = $attributes;
		}
	}
	
	/**
	 * Sets one additional tuning attribute for Carrot2 document sources or algorithms.
	 * Please refer to Carrot2 manual for the list of supported attributes for each source
	 * and algorithm.
	 *
	 * @param $key attribute key
	 * @param $value attribute value
	 */
	public function setAttribute($key, $value) {
		$this->attributes [$key] = $value;
	}
	
	public function getAttributes() {
		return $this->attributes;
	}
}
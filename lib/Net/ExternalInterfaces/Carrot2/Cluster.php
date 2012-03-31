<?php
namespace Net\ExternalInterfaces\Carrot2;

/**
 * Represents a Carrot2 cluster.
 */
class Cluster {
	private $label;
	private $score;
	private $documentIds = array ();
	private $allDocumentIds;
	private $subclusters = array ();
	
	public function __construct($label, $score, $documentIds, $subclusters) {
		$this->label = $label;
		$this->score = $score;
		$this->documentIds = $documentIds;
		$this->subclusters = $subclusters;
	}
	
	/**
	 * Returns this cluster's label.
	 */
	public function getLabel() {
		return $this->label;
	}
	
	function getScore() {
		return $this->score;
	}
	
	/**
	 * Returns the actual size of this cluster, which is the number of unique
	 * documents in the cluster and its subclusters.
	 */
	public function size() {
		if (! $this->allDocumentIds) {
			$this->allDocumentIds = array ();
			$this->addDocumentIds ( $this->allDocumentIds );
		}
		return count ( $this->allDocumentIds );
	}
	
	/**
	 * Returns subclusters of this cluster.
	 */
	public function getSubclusters() {
		return $this->subclusters;
	}
	
	/**
	 * Returns identifiers of documents assigned directly to this cluster (not
	 * the subclusters). Use these identifiers to retrieve documents from the array
	 * returned by Carrot2Result::getDocuments().
	 */
	public function getDocumentIds() {
		return $this->documentIds;
	}
	
	/**
	 * Returns identifiers of documents assigned to this cluster and its
	 * subclusters. Use these identifiers to retrieve documents from the array
	 * returned by Carrot2Result::getDocuments().
	 */
	public function getAllDocumentIds() {
		return $this->allDocumentIds;
	}
	
	/**
	 * Recursive function for collecting document ids from subclusters.
	 */
	private function addDocumentIds(&$ids) {
		foreach ( $this->documentIds as $id ) {
			$ids [$id] = $id;
		}
		
		foreach ( $this->subclusters as $subcluster ) {
			$subcluster->addDocumentIds ( $ids );
		}
		
		return $ids;
	}
}
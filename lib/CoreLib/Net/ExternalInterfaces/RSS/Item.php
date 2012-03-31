<?php
namespace Net\ExternalInterfaces\RSS;

class Item extends \Net\ExternalInterfaces\Internal\DownloadableItem {
	var $title;
	var $description;
	var $document;
	
	function isEmpty(){
		if(!$this->title || !$this->link){
			return true;
		}
	}
	
	function __construct(Document $document){
		$this->document = $document;
	}
	
	/**
	 * @return the $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @return the $link
	 */
	public function getLink() {
		return $this->link;
	}

	/**
	 * @return the $description
	 */
	public function getDescription() {
		return $this->description;
	}
}
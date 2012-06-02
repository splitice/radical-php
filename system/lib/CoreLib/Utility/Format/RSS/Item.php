<?php
namespace Utility\Net\External\RSS;

use Utility\Net\External\DownloadableItem;

class Item extends DownloadableItem {
	private $channel;
	protected $title;
	protected $description;
	protected $link;
	protected $pubDate;
	
	function isEmpty(){
		if(!$this->title || !$this->link){
			return true;
		}
	}
	
	function __construct(Document $document){
		$this->document = $document;
		parent::__construct($this->link);
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
<?php
namespace DDL\TitleParse\Natural\Types;

class Movie extends Internal\NaturalBase {
	protected $title;
	protected $year;
	protected $quality;
	
	function __construct($str){
		parent::__construct($str);
		$this->Parse();
	}
	
	function Parse(){
		$m = array();
		if(
			preg_match('#(.+)\s*\(([1-3][0-9]{3})\)(?:\s*([a-z0-9]+))?#i', $this->rls,$m) ||
			preg_match('#(.+)\s*\[([1-3][0-9]{3})\](?:\s*([a-z0-9]+))?#i', $this->rls,$m)
		){
			$this->title = $m[1];
			$this->year = (int)$m[2];
			if(isset($m[3]))
				$this->quality = $m[3];
		}else{
			$this->isValid(false);
		}
	}
	
	/**
	 * @return the $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @return the $year
	 */
	public function getYear() {
		return $this->year;
	}

	/**
	 * @return the $quality
	 */
	public function getQuality() {
		return $this->quality;
	}
}
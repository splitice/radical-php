<?php
namespace DDL\TitleParse\Natural\Types;

class Application extends Internal\NaturalBase {
	protected $name;
	
	static $keywords = array(
		'portable','cracked','crack','serial','keygen','download','full','activated'
	);
	
	function __construct($str){
		parent::__construct($str);
		$this->Parse();
	}
	
	static function isVersion($v){
		if(preg_match('#^v?([0-9])#',$v)){
			return true;
		}
		return false;
	}
	static function cleanString($a){
		return implode(' ',$a);
	}
	
	function Parse(){
		$parts = preg_split('#([\s|/.]+)#', $this->rls);
		foreach($parts as $pk=>$p){
			if(
				self::isVersion($p)
						||
				(($p=='v' || $p=='V') && self::isVersion($parts[$pk+1]))
			){
				$this->name = self::cleanString(array_slice($parts, 0, $pk));
				break;
			}
		}
	}
	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}
	public function getCleanName(){
		$str = $this->name;
		$str = str_ireplace(self::$keywords, '', $str);
		return $str;
	}
}
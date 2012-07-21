<?php
namespace Basic\Arr\Object;

/**
 * A collection that is always sorted
 * 
 * @author SplitIce
 *
 */
class SortedCollectionObject extends CollectionObject {
	private $function;
	
	/**
	 * Takes the function that will be used for sorting.
	 * 
	 * @param callback $function sorting function
	 */
	function __construct($function){
		$this->function = $function;
	}
	
	/* (non-PHPdoc)
	 * @see \Basic\Arr\Object\CollectionObject::_Set()
	 */
	protected function _Set($k,$v){
		parent::_Set($k,$v);
		usort($this->data, $this->function);
	}
}
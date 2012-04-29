<?php
namespace DDL\Hosts\Internal;

use Basic\ArrayLib\Object\CollectionObject;

class MultiUpload {
	protected $module;
	protected $file;
	protected $file_number;
	protected $data = array();
	
	function __construct(MultiModule $module, $file, $file_number = null, $data = array()){
		$this->module = $module;
		$this->file = $file;
		$this->file_number = $file_number;
		$this->data = new CollectionObject($data);
	}
	
	/**
	 * @return the $file_number
	 */
	public function getFileNumber() {
		return $this->file_number;
	}

	/**
	 * @return the $module
	 */
	public function getModule() {
		return $this->module;
	}

	/**
	 * @param MultiModule $module
	 */
	public function setModule($module) {
		$this->module = $module;
	}

	/**
	 * @return the $file
	 */
	public function getFile() {
		return $this->file;
	}
	
	function getHost(){
		return $this->module->getHost();
	}

	function Upload(){
		return $this->module->getModule()->Upload($this->file);
	}
	
	private $onFailure;
	
	/**
	 * @param field_type $onFailure
	 */
	public function setOnFailure($onFailure) {
		$this->onFailure = $onFailure;
	}

	function onFailure(){
		if($this->onFailure){
			$o = $this->onFailure;
			call_user_func($o);
		}
		$this->module->onFailure($this, $this->data);
	}
	
	function Clear(){
		$this->module = null;
	}
}
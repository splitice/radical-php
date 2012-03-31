<?php
namespace DDL\Hosts\Check\Internal;

class CheckReturn {
	protected $status;
	protected $filename;
	protected $filesize;
	
	protected $module;
	protected $compressedUrl;
	
	/**
	 * @return the $module
	 */
	public function getModule() {
		return $this->module;
	}

	/**
	 * @return the $compressedUrl
	 */
	public function getCompressedUrl() {
		return $this->compressedUrl;
	}

	/**
	 * @param field_type $module
	 */
	public function setModule($module) {
		$this->module = $module;
	}

	/**
	 * @param field_type $compressedUrl
	 */
	public function setCompressedUrl($compressedUrl) {
		$this->compressedUrl = $compressedUrl;
	}

	/**
	 * @param field_type $status
	 */
	public function setStatus($status) {
		$this->status = $status;
	}

	/**
	 * @param field_type $filename
	 */
	public function setFilename($filename) {
		$this->filename = $filename;
	}

	/**
	 * @param field_type $filesize
	 */
	public function setFilesize($filesize) {
		$this->filesize = $filesize;
	}

	function __construct($status, $filename = null, $filesize = null) {
		$this->status = $status;
		$this->filename = $filename;
		$this->filesize = $filesize;
	}
	
	function isFileName(){
		return (bool)$this->filename;
	}
	function isFileSize(){
		return (bool)$this->filesize;
	}
	function isStatus(){
		return ($this->status != 'unknown');
	}
	
	/**
	 * @return the $status
	 */
	public function getStatus() {
		return $this->status;
	}
	
	/**
	 * @return the $filename
	 */
	public function getFilename() {
		return $this->filename;
	}
	
	/**
	 * @return the $filesize
	 */
	public function getFilesize() {
		return $this->filesize;
	}
	
	function toArray(){
		return get_object_vars($this);
	}
}
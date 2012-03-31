<?php
namespace DDL\Hosts\Internal;

class MultiModule {
	protected $host;
	protected $module;
	protected $callback;
	protected $multi;
	
	function __construct($host, \DDL\Hosts\Upload\Interfaces\IUploadHost $module, $callback, \DDL\Hosts\Internal\MultiSet $multi = null){
		$this->host = $host;
		$this->module = $module;
		$this->callback = $callback;
		$this->multi = $multi;
	}
	
	/**
	 * @param \DDL\Hosts\Internal\MultiSet $multi
	 */
	public function setMulti($multi) {
		$this->multi = $multi;
	}

	/**
	 * @return the $multi
	 */
	public function getMulti() {
		return $this->multi;
	}

	/**
	 * @return the $host
	 */
	public function getHost() {
		return $this->host;
	}

	/**
	 * @return the $module
	 */
	public function getModule() {
		return $this->module;
	}

	function onFailure($upload, $data){
		if($this->callback){
			call_user_func($this->callback,$upload,$this->multi,$data);
		}else{
			$this->multi->removeUpload($upload);
		}
	}
}
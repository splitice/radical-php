<?php
namespace DDL\Hosts;
class HandleMulti {
	const DELAYED_TRIES = 10;
	
	private $modules = array ();
	private $files = array ();
	public $progress;
	var $ftp = false;
	
	function addModule($host, Upload\Interfaces\IUploadHost $module, $onFailureCallback = null) {
		$this->modules [$host] = new Internal\MultiModule($host, $module,$onFailureCallback);
	}
	function removeModule($host){
		unset($this->modules[$host]);
	}
	function addUpload($file) {
		$this->files [] = $file;
	}
	function setProgress($progress){
		$this->progress = $progress;
	}
	
	/**
	 * @return the $progress
	 */
	public function getProgress() {
		return $this->progress;
	}

	function RunValidate($to_run,$attempt) {
		global $_CONFIG;
		
		if (! $to_run) {
			return false;
		}
		
		if ($attempt> $_CONFIG ['upload'] ['MAX_RETRY']) {
			return false;
		}
		
		return true;
	}
	
	function Execute() {
		//The run queue, should contain modules * files instances.
		$toRun = array();
		
		//Create run queue
		foreach ( $this->files as $fn => $f ) {
			foreach ( $this->modules as $host => $module ) {
				$toRun [] = new Internal\MultiUpload($module, $f, $fn);
			}
		}
		
		$set = new Internal\MultiSet($toRun,$this);
		$links = $set->Execute();
		$set->Clear();
		
		$links = $this->processDelayed($links);

		return $links;
	}
	
	private function processDelayed($links){
		//Processed delayed
		foreach($links as $host=>$ll){
			foreach($ll as $lk=>$link){
				if($link instanceof Upload\Struct\DelayReturn){
					//Wait until is time for another check
					while(!$link->isTime()){
						\CLI\Thread::$self->Sleep(1);
					}
					for($i=0;$i<self::DELAYED_TRIES;$i++){
						$r = $link->Call();
						if(is_string($r)){
							$links[$host][$lk] = $r;
							break;
						}
					}
					if(!is_string($links[$host][$lk])){
						unset($links[$host][$lk]);//Didnt complete
					}
				}
			}
		}
		return $links;
	}
	
	function Clear(){
		$this->modules = $this->files = array();
	}
}
?>
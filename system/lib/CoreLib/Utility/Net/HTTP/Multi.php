<?php
namespace HTTP;

class Multi {
	private $transfers = array ();
	private $queue = array ();
	private $mh;
	
	/* Callbacks */
	private $_done;
	
	function setDoneCallback($what) {
		$this->_done = $what;
	}
	
	function __construct() {
		$this->mh = curl_multi_init ();
	}
	private function filterCh($ch){
		curl_setopt($ch,CURLOPT_FORBID_REUSE,true);
		curl_setopt($ch,CURLOPT_FRESH_CONNECT,true);
		curl_setopt($ch,CURLOPT_MAXCONNECTS,1000);
		return $ch;
	}
	function Add(Fetch $obj, $callback, $id = null) {
		$transfer = new Internal\Transfer ( $obj, $this, $callback, $id );
		$this->addTransfer ( $transfer );
	}
	
	function addTransfer(Internal\Transfer $transfer) {
		$this->transfers [] = $transfer;
		curl_multi_add_handle ( $this->mh, $this->filterCh($transfer->getCurl ()) );
	}
	
	function numRunning() {
		return count ( $this->queue );
	}
	
	function _lookForCompleted($execute=true) {
		// Get information about the handle that just finished the work.
		while ( $done = curl_multi_info_read ( $this->mh ) ) {
			// Call the associated listener
			foreach ( $this->transfers as $lk => $listener ) {
				// Strict compare handles.
				if ($listener->getCurl () === $done ['handle']) {
					$error = null;
					if($done['result'] != CURLE_OK){
						$error = curl_error($done ['handle']);
					}
					
					$listener->Call ($error);
					curl_multi_remove_handle ( $this->mh, $done ['handle'] );
					if ($execute) {
						curl_close($done ['handle']);
						if ($this->_done) {
							call_user_func ( $this->_done, $listener->getId () );
						}
					} else {
						$this->queue [] = $listener;
					}
					unset ( $this->transfers [$lk] );
				}
			}
		
		}
	}
	
	function _timeoutCheck($execute=false){
		foreach($this->transfers as $tk=>$t){
			if($t->Expired()){
				$e = null;
				//Store exception so as to continue the removal process
				try {
					if($execute){
						$t->onError ('Timeout connecting to '.$t->getObj()->curl[CURLOPT_URL]);
					}
				}catch(\Exception $ex){
					$e = $ex;
				}
				
				//Remove and Close curl handle
				curl_multi_remove_handle($this->mh, $t->getCurl());
				curl_close($t->getCurl());
				
				//Do Multi Done callback
				//Dont do if an exception has already been lodged
				if ($this->_done && !$e) {
					try {
						call_user_func ( $this->_done, $t->getId () );
					}catch(\Exception $ex){
						$e = $ex;
					}
				}
				
				//Remove from transfers list
				unset($this->transfers[$tk]);
				
				//Resume exception
				if($e){
					throw $e;
				}
			}
		}
	}
	
	private function _runCheck($old_running,$running,$execute){
		//Running total has decreased, look (and possibly process) completed
		if($old_running>$running){
			$this->_lookForCompleted ($execute);
			return $running;
		}
		
		return $old_running;
	}
	
	/**
	 * Process incomming data but do not Execute callbacks, queue for later
	 */
	function Execute($execute = false) {
		$running = count($this->transfers);
		do {
			$old_running = $running;
			//Exec until there's no more data in this iteration. This function has a bug, it
			do{
				$execrun = curl_multi_exec ( $this->mh, $running );	
				$old_running = $this->_runCheck($old_running, $running, $execute);
			}
			while ( $execrun == CURLM_CALL_MULTI_PERFORM );
			
			if($execrun === CURLM_OK && $running){
				if (curl_multi_select($this->mh) != -1) {
			        do {
			            $execrun = curl_multi_exec($this->mh, $running);
			            $old_running = $this->_runCheck($old_running, $running, $execute);
			        } while ($execrun == CURLM_CALL_MULTI_PERFORM);
			    }
			}
			
			$old_running = $this->_runCheck($old_running, $running, $execute);
			
			if (! $running)
				break;
			
			// I don't know what these lines do, but they are required for the script to work.
			$i = 0;
			do{
				$i++;
				$res = curl_multi_select ( $this->mh, 1 );
				$this->_timeoutCheck($execute);
			}
			while ( $res === 0 && $i<50 );

			if ($res === false)
				break; // Select error, should never happen.
		} while ( $running );
	}
	/**
	 * Process incomming data and execute callbacks
	 */
	function ExecuteAndProcess() {
		//Queued responses
		foreach ( $this->queue as $qk => $q ) {
			$q->Call ();
			unset ( $this->queue [$qk] );
		}
		
		$this->Execute ( true );
	}

	function ShortExecute($execute = true) {
		//Exec until there's no more data in this iteration. This function has a bug, it
		do{
			$execrun = curl_multi_exec ( $this->mh, $running );	
		}
		while ( $execrun == CURLM_CALL_MULTI_PERFORM );
		if ($execrun != CURLM_OK) { // This should never happen. Optional line.
			throw new \Exception("This should never happen... curl_multi_exec != CURLM_OK");
		}
		
		$this->_lookForCompleted ($execute);
	}
	
	function __destruct() {
		curl_multi_close ( $this->mh );
		foreach ( $this->transfers as $k => $t ) {
			unset ( $t, $this->transfers [$k] );
		}
	}
}
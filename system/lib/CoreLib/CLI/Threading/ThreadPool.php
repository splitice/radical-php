<?php
namespace CLI\Threading;

// TODO: No, not ready yet.
class ThreadPool {
	private $number;
	private $running = array ();
	private $innactive = array ();
	private $communication;
	private $channel;
	function __construct($number) {
		$this->threads = $number;
		$this->communication = new ReadWriteSocketPair ();
		$this->channel = new ThreadChannel ();
	}
	function Add(ActiveObject $thread) {
		while ( count ( $this->running ) >= $this->number ) {
			list ( $pid, ) = unpack ( 'L', $this->communication->Read ( $len ) );
			$this->innactive [$k] = $this->running [$k];
			unset ( $this->running [$k] );
		}
		if (count ( $this->innactive )) {
			$this->channel->Put ( $thread );
		} else {
			$thread->Run ( array (
					$this,
					'_Done' 
			) );
		}
		
		$this->running [] = $thread;
	}
	private function _ThreadLookup($pid) {
		foreach ( $this->running as $k => $t ) {
			if ($t->pid == $pid) {
				return $k;
			}
		}
	}
	function _Remove($thread) {
		$this->communication->Write ( pack ( 'N', getmypid () ) );
	}
	private function _InactiveLoop() {
		$thread = $this->channel->Take ();
		$thread->PerformWork ();
		$this->_Remove ( $thread );
	}
	function _Done($thread) {
		$this->_Remove ( $thread );
		$this->_InactiveLoop ();
	}
}
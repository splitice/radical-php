<?php
namespace Utility\Net\External\rTorrent;

use Utility\Format;

class Torrent extends _CORE {
	var $hash;
	var $host;
	
	function __construct($hash, $host) {
		$this->hash = $hash;
		$this->host = $host;
	}
	
	function stop() {
		$this->POST ( 'd.stop', array ($this->hash ) );
	}
	function close() {
		$this->POST ( 'd.close', array ($this->hash ) );
	}
	function eraseData() {
		$this->POST ( 'd.delete_tied', array ($this->hash ) );
		$this->POST ( 'd.erase', array ($this->hash ) );
	}
	
	function start() {
		$this->POST ( 'd.start', array ($this->hash ) );
	}
	
	function getPath() {
		return $this->POST ( 'd.get_base_path', array ($this->hash ) );
	}
	function getTorrentFile() {
		return $this->POST ( 'd.get_tied_to_file', array ($this->hash ) );
	}
	function getName() {
		return basename ( $this->getPath () );
	}
	function getTorrent() {
		$tf = $this->getTorrentFile ();

		if(is_array($tf)){
			return false;
		}
		$torrent = Format\Torrent::fromFile ( $tf );
		//die(var_dump($torrent));
		
		return $torrent;
	}
	function isCompleted() {
		$path = $this->getPath () . '.finished';
		
		return file_exists ( $path );
	}
}
?>
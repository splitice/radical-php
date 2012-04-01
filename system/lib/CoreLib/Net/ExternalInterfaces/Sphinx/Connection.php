<?php
namespace Net\ExternalInterfaces\Sphinx;
use Database\DBAL\Adapter;

class Connection extends Adapter\Connection {
	const USERNAME = 'root';
	const PASSWORD = '';
	const DATABASE = null;
	
	function __construct($host = '127.0.0.1', $port = 9306, $compression=true){
		parent::__construct($host,static::USERNAME,static::PASSWORD,static::DATABASE,$port,$compression);
	}

	function Escape($string,$allow_wild = true){
		$chars = array ('!', '-', '/', '\\', '|', '&', '^', '$', '#', '@', '(', ')', '~', '"', "'", '<', '>', '+', '.', '%', ':' );
		if(!$allow_wild){
			$chars[] = '*';
		}
		$str = str_replace ( $chars, ' ', $str );
		$str = trim ( preg_replace ( '#\s+#', ' ', $str ) );
		$str = '\''.$str.'\'';
		
		return $str;
	}
	
	function Search($sql, $ranker = 'bm25', $max_matches = 10000, $cutoff = 10000) {
		return $this->Query ( $sql . ' OPTION ranker=' . $ranker . ',max_matches=' . $max_matches );
	}

	static function fromArray(array $from){
		if(!isset($from['host'])){
			$from['host'] = '127.0.0.1';
		}
		if(!isset($from['port'])){
			$from['port'] = 3306;
		}
		if(!isset($from['compression'])){
			$from['compression'] = false;
		}
		return new static($from['host'],$from['port'],$from['compression']);
	}
}
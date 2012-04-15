<?php
namespace Basic\DateTime;

class Date extends Timestamp {
	const DATABASE_FORMAT = "Y-m-d";
	
	function getDay(){
		return (int)$this->toFormat('j');
	}
	function getMonth(){
		return (int)$this->toFormat('n');
	}
	function getYear(){
		return (int)$this->toFormat('Y');
	}
	
	static function fromSQL($d) {
		return new static(strtotime ( $d ));
	}
	
	function toSQL() {
		return $this->toFormat ( static::DATABASE_FORMAT );
	}
}
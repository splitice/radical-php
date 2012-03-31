<?php
namespace Basic\DateTime;

class Date extends Timestamp {
	static function fromSQL($d) {
		return new static(strtotime ( $d ));
	}
	
	function toTimeStamp($i) {
		return date ( "Y-m-d", $this->timestamp );
	}
}
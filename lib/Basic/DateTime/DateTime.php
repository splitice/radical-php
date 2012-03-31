<?php
namespace Basic\DateTime;

class DateTime extends Timestamp {
	static function fromSQL($d) {
		return new static(strtotime ( $d ));
	}
	
	function toSQL() {
		return date ( "Y-m-d H:i:s", $this->timestamp );
	}
}
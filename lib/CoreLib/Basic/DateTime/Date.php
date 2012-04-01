<?php
namespace Basic\DateTime;

class Date extends Timestamp {
	const DATABASE_FORMAT = "Y-m-d";
	
	static function fromSQL($d) {
		return new static(strtotime ( $d ));
	}
	
	function toSQL() {
		return $this->toFormat ( static::DATABASE_FORMAT );
	}
}
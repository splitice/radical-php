<?php
namespace Basic\DateTime;

class DateTime extends Timestamp {
	const DATABASE_FORMAT = "Y-m-d H:i:s";
	
	static function fromSQL($d) {
		return new static(strtotime ( $d ));
	}
	
	function toSQL() {
		return $this->toFormat ( static::DATABASE_FORMAT );
	}
}
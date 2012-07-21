<?php
namespace Basic\DateTime;

class Date extends DateTimeShared {
	const DATABASE_FORMAT = "Y-m-d";
	
	static function fromRaw($year,$month = null,$day = null){
		return new static(mktime(null,null,null,$month,$day,$year));
	}
}
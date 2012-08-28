<?php
namespace CLI\Output;

class General extends Internal\OutputBase {
	static function header($str){
		echo static::E('====[ ',$str," ]====\r\n");
	}

}
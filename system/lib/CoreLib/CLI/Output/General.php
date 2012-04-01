<?php
namespace CLI\Output;

class General extends Internal\OutputBase {
	static function Header($str){
		echo static::E('====[ ',$str," ]====\r\n");
	}

}
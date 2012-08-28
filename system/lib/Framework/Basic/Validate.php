<?php
namespace Basic;

class Validate {
	static function test($what,$validation){
		if(is_string($validation)){
			$validation = new $validation();
		}
	}
}
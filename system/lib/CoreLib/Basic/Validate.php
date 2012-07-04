<?php
namespace Basic;

class Validate {
	static function Test($what,$validation){
		if(is_string($validation)){
			$validation = new $validation();
		}
	}
}
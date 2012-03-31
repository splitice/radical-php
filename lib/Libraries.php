<?php
class Libraries {
	static function path($path){
		return Autoloader::$instance->resolve($path);
	}
}
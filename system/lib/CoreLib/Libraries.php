<?php
class Libraries {
	static function path($path){
		return Autoloader::resolve($path);
	}
}
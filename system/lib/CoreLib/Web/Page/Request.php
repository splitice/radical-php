<?php
namespace Web\Page;

class Request {
	static $headers = array();
	
	static function fromRequest(){
		foreach ($_SERVER as $name => $value)
		{
			if (substr($name, 0, 5) == 'HTTP_')
			{
				$name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
				static::$headers[$name] = $value;
			} else if ($name == "CONTENT_TYPE") {
				static::$headers["Content-Type"] = $value;
			} else if ($name == "CONTENT_LENGTH") {
				static::$headers["Content-Length"] = $value;
			}
		}
	}
	
	static function header($header){
		if(isset(static::$headers[$header]))
			return static::$headers[$header];
	}
}
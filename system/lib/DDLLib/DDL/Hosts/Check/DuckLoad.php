<?php
namespace DDL\Hosts\Check;

class DuckLoad extends Internal\HostBase {
	const HOST_SCORE = 0.8;
	const HOST_ABBR = 'DL';
	const HOST_DOMAIN = 'duckload.com';
	const HOST_REGEX = 'duckload\.com\/download\/([0-9]+)\/([A-Za-z0-9\-_\.]+)';
	
	function Check($url){
		return new Internal\CheckReturn('dead');
	}
}
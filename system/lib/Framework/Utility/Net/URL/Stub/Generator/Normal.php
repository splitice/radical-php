<?php
namespace Utility\Net\URL\Stub\Generator;

/**
 * Generate URL Stubs by replacing all non alpha numeric
 * characters with -'s.
 * 
 * @author SplitIce
 */
class Normal implements IStubGenerator {
	static function generate($value){
		$value = preg_replace('#(^[a-zA-Z0-9])+#', '-', $value);
		return $value;
	}
}
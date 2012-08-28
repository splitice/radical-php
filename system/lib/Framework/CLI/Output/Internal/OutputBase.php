<?php
namespace CLI\Output\Internal;
use CLI\Output\OutputHandler;
use CLI\Output;

abstract class OutputBase extends OutputHandler {
	static function e(){
		$s = '';
		foreach(func_get_args() as $a){
			$s .= $a;
		}
		Output\Log::Get()->Write($s);
		return $s;
	}
}
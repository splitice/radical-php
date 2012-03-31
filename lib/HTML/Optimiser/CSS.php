<?php
namespace HTML\Optimiser;
use HTML\Optimiser\Interfaces\IOptimiser;

class CSS implements IOptimiser {
	static function Optimise($buffer){
		// First stage basic minification
		$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    	$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
    	//TODO: CSS Optimisation
	  	
		return $buffer;
	}
}
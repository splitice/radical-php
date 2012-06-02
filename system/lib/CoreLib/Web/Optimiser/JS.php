<?php
namespace HTML\Optimiser;
use HTML\Optimiser\Interfaces\IOptimiser;

class JS implements IOptimiser {
	private static function GoogleMin($script){
		return $script;
		$ch = curl_init('http://closure-compiler.appspot.com/compile');
		 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'output_info=compiled_code&output_format=text&compilation_level=SIMPLE_OPTIMIZATIONS&js_code=' . urlencode($script));
		$output = curl_exec($ch);
		curl_close($ch);
		
		return $output;
	}
	static function Optimise($buffer){
		try{
			$j = Javascript\JSMinPlus::minify($buffer,'');
			if(strlen($j) < strlen($buffer)){
				$buffer = $j;
			}
		}catch(\Exception $e){
		}
		
		$j = Javascript\JSMin::minify($buffer);
		if(strlen($j) < strlen($buffer)){
			$buffer = $j;
		}
		
		$j = self::GoogleMin($buffer);
		if($j && (strlen($j) < strlen($buffer))){
			$buffer = $j;
		}
		return $buffer;
	}
}
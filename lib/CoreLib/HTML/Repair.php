<?php
namespace HTML;

class Repair {
	static function OptimizedRepair($input){
		$tidy = new \tidy ();
		$buffer = $tidy->repairString ( $input, array ('input-xml' => false, 'output-xml' => false, 'output-xhtml' => true, 'indent-spaces' => 1, 'tab-size' => 1, 'clean' => true, 'wrap' => 0 ), 'utf8' );
		
		return $buffer;
	}
}
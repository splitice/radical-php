<?php
namespace Utility\Payment;

use Utility\Payment\Modules\IPaymentModule;

/**
 * When dealing with finances its important to log raw 
 * data reliably, so we implement a really low level logging
 * mechanism.
 * 
 * @author SplitIce
 *
 */
class Logging {
	function __construct($module){
		if($module instanceof IPaymentModule){
			$module = array_pop(explode('\\',get_class($module)));
		}
		$this->module = $module;
	}
	
	function getFileName(){
		global $BASEPATH;
		$file = $BASEPATH.'/payment.log';
		return $file;
	}
	
	function log($text){
		$fp = fopen ( $this->getFileName(), 'a' );
		fwrite ( $fp, '['.$this->module.'] '.$text . "\n\n" );
		
		fclose ( $fp ); // close file
	}
}
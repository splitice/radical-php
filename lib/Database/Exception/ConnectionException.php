<?php
namespace Database\Exception;
class ConnectionException extends DatabaseException {
	function __construct($connect_string,$extra='') {
		$message = 'Couldnt connect to "' . $connect_string . '"';
		if($extra){
			$message .= '. '.$extra;
		}
		parent::__construct ( $message );
	}
}
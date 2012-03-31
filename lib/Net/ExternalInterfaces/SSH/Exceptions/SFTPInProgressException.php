<?php
namespace Net\ExternalInterfaces\SSH\Exceptions;

class SFTPInProgressException extends \Exception {
	function __construct($message){
		parent::__construct('SFTP Is in progress, '.$message);
	}
}
<?php
namespace Utility\Utility\Net\External\SSH\Exceptions;

class SFTPInProgressException extends \Exception {
	function __construct($message){
		parent::__construct('SFTP Is in progress, '.$message);
	}
}
<?php
namespace Utility\Net\SSH\Exceptions;

class SSHException extends \Exception {
	function __construct($message){
		parent::__construct('SSH Ex: '.$message);
	}
}
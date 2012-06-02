<?php
namespace Model\Database\Exception;
use ErrorHandling\Errors\Internal\ErrorBase;

abstract class DatabaseException extends ErrorBase {
	function __construct($message, $heading = 'Database Error') {
		parent::__construct($message,$heading);
	}
}
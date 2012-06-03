<?php
namespace Model\Database\Exception;
use Core\ErrorHandling\Errors\Internal\ErrorBase;

abstract class DatabaseException extends ErrorBase {
	function __construct($message, $heading = 'Database Error') {
		parent::__construct($message,$heading);
	}
}
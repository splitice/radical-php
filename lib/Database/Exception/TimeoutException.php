<?php
namespace Database\Exception;
class TimeoutException extends DatabaseException {
	function __construct($sql) {
		parent::__construct ( 'Query "' . $connect_string . '" timed out.' );
	}
}
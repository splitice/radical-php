<?php
namespace Database\Exception;
class FetchException extends DatabaseException {
	function __construct($message = 'Error fetching data from Database') {
		parent::__construct ( $message );
	}
}
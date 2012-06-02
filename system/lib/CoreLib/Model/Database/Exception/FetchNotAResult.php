<?php
namespace Database\Exception;

class FetchNotAResult extends FetchException {
	function __construct() {
		parent::__construct ( 'SQL Fetch attempted on return value that had no data' );
	}
}
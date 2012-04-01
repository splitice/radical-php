<?php
namespace Database\Exception;
class QueryError extends DatabaseException {
	function __construct($sql,$error='Unknown') {
		parent::__construct ( 'Error executing "'.substr($sql,0,50).'", Error: '.$error );
	}
}
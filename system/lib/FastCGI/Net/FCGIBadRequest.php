<?php
namespace Net;

class FCGIBadRequest extends \Exception {
	function __construct() {
		parent::__construct ( 'Bad Request' );
	}
}
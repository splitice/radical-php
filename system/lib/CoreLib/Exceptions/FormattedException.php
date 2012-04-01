<?php
namespace Exceptions;

class FormattedException extends \Exception {
	function __construct(){
		$args = func_get_args();
		parent::__construct(call_user_func_array('sprintf',$args));
	}
}
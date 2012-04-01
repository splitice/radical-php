<?php
namespace Exceptions;
class FileNotExists extends \Exception {
	function __construct($file){
		return parent::__construct($file .' does not exist');
	}
}
?>
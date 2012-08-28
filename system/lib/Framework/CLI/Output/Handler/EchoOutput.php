<?php
namespace CLI\Output\Handler;

class EchoOutput implements IOutputHandler {
	function output($string){
		echo $string;
	}
}
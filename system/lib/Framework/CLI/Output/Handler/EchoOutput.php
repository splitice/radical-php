<?php
namespace CLI\Output\Handler;

class EchoOutput implements IOutputHandler {
	function Output($string){
		echo $string;
	}
}
<?php
namespace HTTP\Server;
class static_response {

	var $content_length;

	function static_response($str) {

		$this->str = $str;
		$this->content_length = strlen ( $str );

	}

	function parser_open($args, $filename, &$rq_err, &$cgi_headers) {

	}

	function parser_get_output() {

		$s = $this->str;
		$this->str = "";
		return ($s);

	}

	function parser_eof() {

		return ($this->str === "");

	}

	function parser_close() {

	}

}
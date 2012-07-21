<?php
namespace Web\Resource\Javascript\Libraries;

interface IJavascriptLibrary {
	function __construct($version);
	function __toString();
}
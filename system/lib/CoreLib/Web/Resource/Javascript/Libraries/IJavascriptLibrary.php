<?php
namespace Web\Resource\Libraries;

interface IJavascriptLibrary {
	function __construct($version);
	function __toString();
}
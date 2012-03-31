<?php
namespace HTML\Javascript\Libraries;

interface IJavascriptLibrary {
	function __construct($version);
	function __toString();
}
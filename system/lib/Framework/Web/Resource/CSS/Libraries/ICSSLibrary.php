<?php
namespace Web\Resource\CSS\Libraries;

interface ICSSLibrary {
	function __construct($version);
	function __toString();
}
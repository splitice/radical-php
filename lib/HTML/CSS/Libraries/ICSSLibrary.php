<?php
namespace HTML\CSS\Libraries;

interface ICSSLibrary {
	function __construct($version);
	function __toString();
}
<?php
namespace Web\Templates\Adapter;

use Web\Templates\Scope;

interface ITemplateAdapter {
	function __construct(\File\Instance $file);
	function Output(Scope $_);
	static function is(\File\Instance $file);
}
<?php
namespace Web\Templates\Adapter;

interface ITemplateAdapter {
	function __construct(\File\Instance $file);
	function Output(array $variables, $handler);
	static function is(\File\Instance $file);
}
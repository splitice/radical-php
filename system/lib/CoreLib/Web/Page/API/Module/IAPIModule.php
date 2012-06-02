<?php
namespace Web\API\Module;

interface IAPIModule {
	static function canType($type);
	function can($method);
}
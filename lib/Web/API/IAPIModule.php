<?php
namespace Web\API;

interface IAPIModule {
	static function canType($type);
	function can($method);
}
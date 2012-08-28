<?php
namespace Utility\Image\Filters\Interfaces;

interface IExternalFilter {
	function toData();
	static function filter($gd,$data);
}
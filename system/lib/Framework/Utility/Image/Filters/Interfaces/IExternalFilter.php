<?php
namespace Utility\Image\Filters\Interfaces;

interface IExternalFilter {
	function toData();
	static function Filter($gd,$data);
}
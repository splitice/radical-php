<?php
namespace Utility\Net\External\ContentAPI\Interfaces;

interface IFromURL {
	static function recogniseURL($url);
	static function fromURL($url);
}
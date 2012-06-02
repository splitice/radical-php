<?php
namespace Utility\Net\External\ContentAPI\Interfaces;

interface IFromURL {
	static function RecogniseURL($url);
	static function fromURL($url);
}
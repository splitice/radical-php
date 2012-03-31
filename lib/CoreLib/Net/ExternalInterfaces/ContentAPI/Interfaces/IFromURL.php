<?php
namespace Net\ExternalInterfaces\ContentAPI\Interfaces;

interface IFromURL {
	static function RecogniseURL($url);
	static function FromURL($url);
}
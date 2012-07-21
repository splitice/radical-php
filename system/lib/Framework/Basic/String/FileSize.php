<?php
namespace Basic\String;

class FileSize {
	/**
	 * Returns a human readable filesize
	 *
	 * @author      wesman20 (php.net)
	 * @author      Jonas John
	 * @version     0.3
	 * @link        http://www.jonasjohn.de/snippets/php/readable-filesize.htm
	 */
	static function HumanReadableFilesize($size) {
		$mod = 1024;
	
		$units = explode(' ','B KB MB GB TB PB');
		for ($i = 0; $size > $mod; $i++) {
			$size /= $mod;
		}
	
		return round($size, 2) . ' ' . $units[$i];
	}
}
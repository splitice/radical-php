<?php
namespace Utility\DDL\Hosts\Check\Interfaces;

interface IDDLHostCheck {
	function recognise($data);
	function recogniseSingle($link);
	function recogniseAll($data);
	function compressURL($url);
	function extractURL($url);
	function AppendFilename($url,$filename);
	/**
	 * @param string $url
	 * @return \DDL\Hosts\Check\Internal\CheckReturn
	 */
	function check($url);
	function checkURLs(array $urls);
	function checkMulti($mh,$url,$callback);	
	function getAbbr();	
	function getClassName();
}
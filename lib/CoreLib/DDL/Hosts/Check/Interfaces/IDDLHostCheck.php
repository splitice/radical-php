<?php
namespace DDL\Hosts\Check\Interfaces;

interface IDDLHostCheck {
	function Recognise($data);
	function RecogniseSingle($link);
	function RecogniseAll($data);
	function CompressURL($url);
	function ExtractURL($url);
	function AppendFilename($url,$filename);
	/**
	 * @param string $url
	 * @return \DDL\Hosts\Check\Internal\CheckReturn
	 */
	function Check($url);
	function CheckURLs(array $urls);
	function CheckMulti($mh,$url,$callback);	
	function getAbbr();	
	function getClassName();
}
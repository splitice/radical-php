<?php
namespace File\Format\Grouping\ParsedFilename;

abstract class ParsedFilename {
	protected $fullPath;
	protected $basefilename;
	protected $filetype;
	
	function __construct($link, $basefilename, $filetype){
		$this->fullPath = $fullPath;
		$this->basefilename = $basefilename;
		$this->filetype = $filetype;
	}

	/**
	 * @return the $fullPath
	 */
	public function getFullPath() {
		return $this->fullPath;
	}

	/**
	 * @return the $basefilename
	 */
	public function getBasefilename() {
		return $this->basefilename;
	}

	/**
	 * @return the $filetype
	 */
	public function getFiletype() {
		return $this->filetype;
	}
	
}
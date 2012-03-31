<?php
namespace File\Format\Grouping\ParsedFilename;

class SplitFilename extends ParsedFilename {
	protected $partnum;
	
	function __construct($fullPath, $basefilename, $filetype, $partnum){
		$this->partnum = $partnum;
		parent::__construct($fullPath, $basefilename, $filetype);
	}

	/**
	 * @return the $partnum
	 */
	public function getPartnum() {
		return $this->partnum;
	}
}
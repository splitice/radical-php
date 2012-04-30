<?php
namespace DDL\TitleParse\Scene\Types;

class XVID extends Internal\MovieBase {
	function Parse(){
		if(!$this->parts){
			return;
		}
		$parts_backup = $this->parts;
		parent::Parse();
		if(strtoupper($this->encoding) != 'XVID' && strtoupper($this->encoding) != 'DIVX'){
			$this->isValid(false);
		}
		if(!$this->valid){
			$this->valid = true;
			$this->parts = $parts_backup;
			parent::BackupParse();
			if(strtoupper($this->encoding) != 'XVID' && strtoupper($this->encoding) != 'DIVX'){
				$this->isValid(false);
			}
		}
	}
}
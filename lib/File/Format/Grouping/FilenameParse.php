<?php
namespace File\Format\Grouping;

class FilenameParse {
	static function parse($filename){
		$f = $filename->getFilename();
		if(!$f){
			return;
		}
		
		$ext = strtolower(self::extractLastCut($f));
		if(!$ext){
			return;
		}
		
		if($ext == 'rar'){
			$ext2 = self::extractLast($f);
			if(substr($ext2, 0, 4) == 'part'){
				
				if(is_numeric($ext2 = substr($ext2, 4))){
					self::extractLastCut($f);
					return new ParsedFilename\SplitFilename($filename, $f, 'rar', (int)$ext2);
				}
				
			} else {
				return new ParsedFilename\SingleFilename($filename, $f, 'rar');
			}
		} elseif($ext == 'zip') {
			return new ParsedFilename\SingleFilename($filename, $f, 'zip');
		} elseif($ext == '7z') {
			return new ParsedFilename\SingleFilename($filename, $f, '7z');
		} elseif(is_numeric($ext)) {
			$ext2 = self::extractLast($f);
			if(substr($ext2, 0, 2) == '7z'){
				self::extractLastCut($f);
				return new ParsedFilename\SplitFilename($filename, $f, '7z', (int)$ext);
			}
			return new ParsedFilename\SplitFilename($filename, $f, 'split', (int)$ext);
		} elseif($ext{0} == 'r') {
			$partnum = substr($ext, 1);
			if(is_numeric($partnum))
				return new ParsedFilename\SplitFilename($filename, $f, 'rar', (int)$partnum);
		} elseif($ext{0} == 'z') {
			$partnum = substr($ext, 1);
			if(is_numeric($partnum))
				return new ParsedFilename\SplitFilename($filename, $f, 'zip', (int)$partnum);
		} 
	}
	
	static function groupByTypeBaseName($filenames){
		$groups = array();
		foreach($filenames as $f){
			$l = self::parse($f);
			if($l){
				$groups[$l->getFiletype().'/'.$l->getBasefilename()][] = $l;
			}
		}
		
		if($filenames && !$groups){
			$groups[0] = array();
			foreach($filenames as $f){
				$groups[0][] = new ParsedFilename\SingleFilename($filename, $f, '', 'unknown');
			}
		}
		
		return $groups;
	}
	
	private static function extractLast($f){
		$pos = strrpos($f, '.');
		$ext = substr($f, $pos + 1); 
		
		return strtolower($ext); 
	}
	
	private static function extractLastCut(&$f){
		$ext = self::extractLast($f);
		$f = substr($f, 0, -1 - strlen($ext));
		
		return $ext; 
	}
}
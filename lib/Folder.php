<?php
class Folder {
	static function ListDir($expr, $recursive = false) {
		if ($recursive) {
			if(is_file($expr)){
				return array($expr);
			}
			
			$items = glob ( $expr . '/*' );
			
			for($i = 0; $i < count ( $items ); $i ++) {
				if (is_dir ( $items [$i] )) {
					$add = glob ( $items [$i] . '/*' );
					$items = array_merge ( $items, $add );
				}
			}
			
			foreach($items as $k=>$v){
				if(is_dir($v)){
					unset($items[$k]);
				}else{
					$items[$k] = realpath($v);
				}
			}
			
			return $items;
		} else {
			return glob ( $expr );
		}
	}
	
	static function SaneCreate($a) {
		if (is_array ( $a )) {
			foreach ( $a as $v ) {
				self::SaneCreate ( $v );
			}
			return true;
		}
		$path = '';
		foreach ( explode ( '/', $a ) as $v ) {
			$path .= '/' . $v;
			self::Create($path);
		}
	}
	static function Create($path){
		if(static::Exists($path)){
			if(is_file($path)){
				throw new \Exception('Folder to create is actually a file!');
			}
			return false;
		}
		@mkdir ( $path );
	}
	
	static function Exists($file){
		return (file_exists($file) && is_dir($file));
	}
	
	static function Copy($from,$to){
		if(!file_exists($from)){
			throw new \Exceptions\FileNotExists($from);
		}
		if(!file_exists($to)){
			throw new \Exceptions\FileNotExists($to);
		}
		$cmd = 'cp -R '.escapeshellarg($from).' '.escapeshellarg($to);
		exec($cmd);
	}
	static function getIterator($path,$options_inner = FilesystemIterator::SKIP_DOTS, $options_outer = RecursiveIteratorIterator::CHILD_FIRST){
		$inner = new RecursiveDirectoryIterator($path,$options_inner);
		return new RecursiveIteratorIterator($inner, $options_outer);
	}
	private static function _Remove($folder){
		if(!file_exists($folder)){
			return false;
		}
		
		$iterator = static::getIterator($folder);
		foreach ($iterator as $path) {
			if ($path->isDir()) {
				rmdir($path->__toString());
			} else {
				unlink($path->__toString());
			}
		}
		
		rmdir($folder);
	} 
	static function Remove($folder) {
		$folder = realpath($folder);
		if(!$folder){
			return;//Invalid
		}
		
		//Delete if is file
		//TODO: Cleanup code using this
		if(is_file($folder)){
			@unlink($folder);
		}
		
		//echo "Remove: ".$folder,"\r\n";
		if(is_array($folder)){
			foreach($folder as $f){
				self::Remove($f);
			}
		}else{
			static::_Remove($folder);
		}
	}
}
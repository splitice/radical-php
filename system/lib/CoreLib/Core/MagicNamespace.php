<?php
namespace Core;

//TODO: Finish, still incomplete.
class MagicNamespace {
	protected $class;
	protected $library;
	
	function __construct($class,$library){
		$this->class = $class;
		$this->library = $library;
	}
	
	function getMagicPrefix(){
		return '_\\'.$this->library;
	}
	function getMagicClass(){
		return $this->getMagicPrefix().'\\'.ltrim($this->class,'\\');
	}
	function getNamespace(){
		$class = $this->getMagicClass();
		$class = explode('\\',$class);
		array_pop($class);
		
		return implode('\\',$class);
	}
	function getFile(){
		global $BASEPATH;
		
		$expr = $BASEPATH.DIRECTORY_SEPARATOR.'*'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.$this->library.DIRECTORY_SEPARATOR.\Core\Libraries::toPath($this->class).'.php';
		$files = glob($expr);
		$files = array_reverse($files);
		if(!$files){
			throw new \Exception('No File found');
		}
		
		$file = $files[0];
		
		return $file;
	}
	
	function getNamespaceStatement(){
		return 'namespace '.$this->getNamespace().';';
	}
	
	function CreateCode(){
		$file = $this->getFile();
		$data = file_get_contents($file);

		$matchExpr = '#namespace(?:\s+)([\\a-zA-Z0-9]+);#i';
		if(preg_match($matchExpr, $data)){
			$data = preg_replace($matchExpr, $this->getNamespaceStatement(), $data);
		}else{
			$data = preg_replace('#\<\?(php)?#', "<?php\r\n".$this->getNamespaceStatement(), $data);
		}
		
		return $data;
	}
	
	function getCachePath(){
		global $BASEPATH;
		$file = md5($this->getMagicClass());
		return $BASEPATH.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'magic'.DIRECTORY_SEPARATOR.$file.'.php';
	}
	
	function Load(){
		$path = $this->getCachePath();
		if(file_exists($path)){
			if(filemtime($path) == filemtime(\AutoLoader::resolve($this->class))){
				return $path;
			}
		}
		
		file_put_contents($path, $this->CreateCode());
		return $path;
	}
	
	static function fromString($class){
		$class = explode('\\',$class);
		unset($class[0]);
		$library = $class[1];
		unset($class[1]);
		if(!isset($class[2])){
			throw new \Exception('Invalid Format, no Class specified');
		}
		if($class[2] == '_'){
			throw new \Exception('Recursive Magic Namespace reference _\\*\\_\\ is not valid');
		}
		
		$class = implode('\\',$class);
		return new static($class,$library);
	}
}
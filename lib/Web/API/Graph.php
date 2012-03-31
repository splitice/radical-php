<?php
namespace Web\API;
use Image\Graph\Source\IGraphSource;
use Web\PageHandler;
use Image\Graph\Renderer;

class Graph extends APIBase {
	private $graph;
	
	private $width = 900;
	private $height = 300;
	
	function __construct($data,$type){
		$this->graph = $data['graph'];
		
		if(isset($_GET['width'])){
			$this->width = (int)$_GET['width'];
		}
		if(isset($_GET['height'])){
			$this->height = (int)$_GET['height'];
		}
		
		parent::__construct($data,$type);
	}
	static function canType($type){
		switch($type){
			case 'png':
				return true;
		}
	}
	private function _getModule($module){
		return \ClassLoader::getProjectSpace('DB\\'.ucfirst($module));
	}
	function can($module){		
		return class_exists($this->_getModule($module));
	}
	function __call($module,$arguments){
		$module = $this->_getModule($module);

		$method = 'graph'.$this->graph;
		if(method_exists($module, $method)){
			$graph = $module::$method($this->data);

			if($graph instanceof IGraphSource){
				$graph->Setup($this->width,$this->height);
				ob_start();
				if($this->type == 'json'){
					$ret = $graph->Draw(new Renderer\JSON());
				}else{
					$ret = $graph->Draw(new Renderer\Output());
				}
				if($ret === null){
					$ret = ob_get_contents();
				}
				ob_end_clean();
				
				return $ret;
			}
		}
	}
}
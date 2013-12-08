<?php
namespace Web\Page\API\Module;
use Utility\Image\Graph\Source\IGraphSource;
use Web\Page\Handler;
use Utility\Image\Graph\Renderer;

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
	function output_type($type){
		if($type == 'kendo') return 'json';
		return $type;
	}
	static function canType($type){
		switch($type){
			case 'kendo':
			case 'png':
				return true;
		}
	}
	private function _getModule($module,$ns = 'DB'){
		return \Core\Libraries::getProjectSpace($ns.'\\'.ucfirst($module));
	}
	function can($module){		
		return class_exists($this->_getModule($module)) || class_exists($this->_getModule($module,'Graph'));
	}
	function __call($module,$arguments){
		//Work out what we are going to display
		$class = $this->_getModule($module);
		$graph = null;
		if(class_exists($class)){
			$method = 'graph'.$this->graph;
			if(method_exists($class, $method)){
				$graph = $class::$method($this->data);
			}
		}else{
			$class = $this->_getModule($module,'Graph');
			$module = new $class($arguments);
			$graph = $module->{$this->graph}();
		}
		
		//Do the rendering
		if($graph instanceof IGraphSource){
			$graph->Setup($this->width,$this->height);
			ob_start();
			if($this->type == 'json'){
				$ret = $graph->Draw(new Renderer\JSON());
			}elseif($this->type == 'kendo'){
				$ret = $graph->Draw(new Renderer\Kendo());
			}else{
				$ret = $graph->Draw(new Renderer\Output());
			}
			if($ret === null){
				$ret = ob_get_contents();
			}
			ob_end_clean();
		
			return $ret;
		}elseif($graph instanceof \Utility\Image\Graph\Schema\Graph){
			$graph->box->width = $this->width;
			$graph->box->height = $this->height;
			ob_start();
			if($this->type == 'json'){
				$ret = $graph->render(new Renderer\JSON());
			}elseif($this->type == 'kendo'){
				$ret = $graph->render(new Renderer\Kendo());
			}else{
				$ret = $graph->render(new Renderer\Output());
			}
			if($ret === null){
				$ret = ob_get_contents();
			}
			ob_end_clean();
		
			return $ret;
		}
	}
}
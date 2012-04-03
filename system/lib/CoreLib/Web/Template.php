<?php
namespace Web;

use Core\Resource;

use HTML\Form\Builder\FormBuilder;

class Template extends PageHandler\PageBase {
	public $vars = array ('title' => 'Template Error', 'body' => 'No template specified.' );
	public $form;
	
	protected $name = 'error';
	protected $container = 'HTML';
	protected $handler = false;	
	public $JSData;
	
	function __construct($name, $vars = array(), $container = 'HTML') {
		$this->vars = $vars;
		$this->name = $name;
		$this->container = $container;
		$this->file = new Resource($name);
		die(var_dump($this->file));
		$this->form = new FormBuilder();
		foreach(array_slice(debug_backtrace(true),1) as $r){
			if(isset($r['object']) && $r['object'] instanceof PageHandler\IPage && !($r['object'] instanceof Template)){
				$this->handler = $r['object'];
				break;
			}
		}
	}
	
	function __call($method,$args){
		if(!method_exists($this->handler,$method)) return '';
		return call_user_func_array(array($this->handler,$method),$args);
	}
	
	function addVarMember($k,$v){
		$this->vars[$k] = $v;
	}
	
	static function isSupported($path){
		
	}
	static function getPath($name,$output = 'HTML'){
		global $BASEPATH;
		$expr = $BASEPATH . 'template' . DS . $output . DS . $name.'.*';
		foreach(glob($expr) as $path){
			if(static::isSupported($path)){
				
			}
		}
	}
	
	static function Exists($name,$output='HTML'){
		return file_exists(static::getPath($name,$output));
	}
	
	function GET() {		
		$TEMPLATE_FILE = static::getPath($this->name,$this->output);
			
		return $this->Load($TEMPLATE_FILE);
	}
	
	function Load($file,$locals = array()){
		global $_CONFIG;
		$VAR = $this->vars;
		
		$HANDLER = $this->handler;
		
		extract($locals);
		if(!isset($locals['TEMPLATE_FILE'])){
			$TEMPLATE_FILE = $file;
		}
		
		ob_start();
		include ($file);
		$contents = ob_get_contents();
		ob_end_clean();
		
		PageHandler::Top()->headers->Add('Content-Type', 'text/html;charset=utf-8');

		echo $contents;//TODO: GZIP/Optimise
		//$contents = \Optimiser\HTML::Optimise($contents);
		//return new \PageHandler\GZIP($contents); //END OF CHAIN
	}
	function POST() {
		return $this->GET ();
	}
}
?>
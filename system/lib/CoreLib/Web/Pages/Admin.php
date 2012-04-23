<?php
namespace Web\Pages;

use Basic\Arr;

use Web\Template;

use Database\Model\TableReferenceInstance;
use Web\Session\User\IUserAdmin;
use Net\URL\Pagination\QueryMethod;
use Web\Pages\Special\Redirect;
use Image\Graph\Renderer\Base64String;
use FGV\DB\Block;
use Web\PageHandler;

class Admin extends PageHandler\HTMLPageBase {
	protected $module;
	protected $url;
	
	function __construct(\Net\URL\Path $url,$module){
		$this->module = $module;
		$this->url = $url;
	}
	private function checkAdmin(){
		\Web\Session::$auth->LoggedInArea();
		if(\Web\Session::$auth->isAdmin()){
			return false;
		}
		throw new \Exception('Not an admin');
		return false;
	}
			
	function GET(){
		$page = $this->checkAdmin();
		if($page) return $page;
		
		if($this->module === null){
			$VARS = array();
			$modules = Arr::where(function($k,$v){
				return class_exists($v);
			},\ClassLoader::getNSExpression('\\Web\\Admin\\Modules\\*'));
			$VARS['modules'] = Arr::map(array('*','createLink'), $modules);
			return new Template('index', $VARS,'admin');
		}else{
			$class = '\\Web\\Admin\\Modules\\'.$this->module;
			if(!class_exists($class)){
				throw new \Exception('Couldnt find admin module');
			}
			
			//Remove the module name from the URL path
			$this->url->removeFirstPathElement();
			
			//Delegate to module
			return new $class($this->url);
		}
	}
	function POST(){
		return $this->GET();
	}
}
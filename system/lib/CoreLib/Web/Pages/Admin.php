<?php
namespace Web\Pages;

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
		if(\Web\Session::$data['user'] instanceof IUserAdmin){
			return true;
		}
		return false;
	}
			
	function GET(){
		if(!$this->checkAdmin()) return new Admin\Login($this->url,$this->module);
		
		$class = '\\Web\\Admin\\'.$this->module;
		if(!class_exists($class)){
			throw new \Exception('Couldnt find admin module');
		}
		
		return new $class($this->url);
	}
	function POST(){
		return $this->GET();
	}
}
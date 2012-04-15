<?php
namespace Web\Pages\Admin;

use Database\Model\TableReferenceInstance;
use Web\Session\User\IUserAdmin;
use Net\URL\Pagination\QueryMethod;
use Web\Pages\Special\Redirect;
use Image\Graph\Renderer\Base64String;
use FGV\DB\Block;
use Web\PageHandler;

class Login extends PageHandler\HTMLPageBase {
	protected $module;
	protected $url;
	
	function __construct(\Net\URL\Path $url,$module){
		$this->module = $module;
		$this->url = $data;
	}
			
	function GET(){
		if(\Web\Session::$data === null){
			throw new \Exception('You must have a Web session handler to access the admin panel');
		}
		return \Web\Session::$data->getPage();
	}
	function POST(){
		return $this->GET();
	}
}
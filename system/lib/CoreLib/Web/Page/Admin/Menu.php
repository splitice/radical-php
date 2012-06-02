<?php
namespace Web\Page\Admin;

use Web\Pages\Admin;

use Basic\Arr;
use Web\Template;

/**
 * A sub request that generates the admin menu
 * 
 * @author SplitIce
 */
use Web\Page\Handler\PageBase;

class Menu extends PageBase {
	function GET(){
			$VARS = array();
				
			//Get admin modules
			$modules = Arr::where(function($k,$v){
				return class_exists($v);//is valid class
			},\Core\Libraries::getNSExpression(Admin::CLASS_PATH.'*'));
				
			//Create links to modules
			$VARS['modules'] = Arr::map(array('*','createLink'), $modules);
				
			//Template to show
			return new Template('Common/menu', $VARS,'admin');
	}
	function POST(){
		return $this->GET();
	}
}
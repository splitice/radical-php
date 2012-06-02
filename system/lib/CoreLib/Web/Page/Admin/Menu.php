<?php
namespace Web\Admin;

use Web\Pages\Admin;

use Basic\Arr;
use Web\Template;

/**
 * A sub request that generates the admin menu
 * 
 * @author SplitIce
 */
use Web\PageHandler\PageBase;

class Menu extends PageBase {
	function GET(){
			$VARS = array();
				
			//Get admin modules
			$modules = Arr::where(function($k,$v){
				return class_exists($v);//is valid class
			},\ClassLoader::getNSExpression(Admin::CLASS_PATH.'*'));
				
			//Create links to modules
			$VARS['modules'] = Arr::map(array('*','createLink'), $modules);
				
			//Template to show
			return new Template('Common/menu', $VARS,'admin');
	}
	function POST(){
		return $this->GET();
	}
}
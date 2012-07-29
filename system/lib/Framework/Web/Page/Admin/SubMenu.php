<?php
namespace Web\Page\Admin;

use Basic\Arr;
use Web\Template;

/**
 * A sub request that generates the admin menu
 * 
 * @author SplitIce
 */
use Web\Page\Handler\PageBase;

class SubMenu extends PageBase {
	protected $module;
	protected $selected;
	
	function __construct($module,$selected){
		$this->module = $module;
		$this->selected = $selected;
	}
	
	/**
	 * Handle GET request
	 *
	 * @throws \Exception
	 */
	function GET(){
			$VARS = array();

			//Create links to modules
			$VARS['module'] = $this->module;
			$VARS['selected'] = $this->selected;
				
			//Template to show
			return new Template('Common/submenu', $VARS,'admin');
	}
	/**
	 * Handle POST request
	 *
	 * @throws \Exception
	 */
	function POST(){
		return $this->GET();
	}
}
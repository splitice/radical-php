<?php
namespace Web\Page;

class EventProcessor extends Handler\EventPageBase {
	function Handle(){
		return $this->_processEvent();
	}
}
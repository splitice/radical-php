<?php
namespace Web\Page;

class EventProcessor extends Handler\EventPageBase {
	function handle(){
		return $this->_processEvent();
	}
}
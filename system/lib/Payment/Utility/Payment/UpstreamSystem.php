<?php
namespace Utility\Payment;

class UpstreamSystem extends System {
	private $u;
	function __construct($u,$module,$arg = null){
		$this->u = $u;
		parent::__construct($module,$arg);
	}
	
	function toUrl(){
		return $this->u->toUrl();
	}
	function onReceived(Transaction $p){
		return $this->u->onReceived($p);
	}
	function onSuccess(Transaction $p){
		return $this->u->onSuccess($p);
	}
	function onCancel(Order $p){
		if(method_exists($this->u,'onCancel'))
			return $this->u->onCancel($p);
		
		echo "<html><head><title>Canceled</title></head><body><h3>The order was canceled.</h3>";
		echo "</body></html>";
	}
}
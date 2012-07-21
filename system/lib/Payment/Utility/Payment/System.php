<?php
namespace Utility\Payment;

abstract class System {
	protected $module;
	function __construct($module,$arg = null){
		$class = 'Utility\\Payment\\'.$module;
		if(!class_exists($class)){
			throw new \Exception('Payment module "'.$module.'" doesnt exist');
		}
		$this->module = new $class($this->toUrl(),$arg);
	}
	
	function bill($ammount){
		$this->module->bill($ammount);
	}
	
	function subscribe($ammount){
		$this->module->subscribe($ammount);
	}
	
	function process(){
		switch ($_GET['action']) {
			case 'success' : // Order was successful...
				return $this->onSuccess();
		
			case 'cancel' : // Order was canceled...
				return $this->onCancel();
		
			case 'ipn' : // Paypal is calling page for IPN validation...
				if($this->module->ipn()){
					$this->onReceived();
				}
		
		}
	}
	
	function __call($method,$arguments){
		return call_user_func_array($method,$arguments);
	}
	
	/* Overloadables */
	abstract function toUrl();
	abstract function onReceived(Transaction $p);
	abstract function onSuccess(Transaction $p);
	function onCancel(Order $p){
		echo "<html><head><title>Canceled</title></head><body><h3>The order was canceled.</h3>";
		echo "</body></html>";
	}
}
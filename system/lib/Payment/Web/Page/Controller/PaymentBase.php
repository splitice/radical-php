<?php
namespace Web\Page\Controller;

use Utility\Payment\Order;
use Utility\Payment\Transaction;
use Web\Page\Handler\HTMLPageBase;
use Web\Page\Handler;
use Utility\Payment;

abstract class PaymentBase extends HTMLPageBase {
	protected $system;
	
	function __construct($data,$address = null){		
		$this->system = new Payment\UpstreamSystem($this,$data['module'],$address);
	}
	
	/**
	 * @return Order
	 */
	abstract function getOrder();
	
	private function _GET(){
		if(isset($_GET['action']))
			return $this->system->process();
		else{
			$this->bill($this->getOrder());
		}
	}
	
	protected function bill(Order $order){
		return $this->system->bill($order);
	}
	
	/**
	 * Handle GET request
	 *
	 * @throws \Exception
	 */
	function GET(){
		return $this->_GET();
	}

	/**
	 * Handle POST request
	 *
	 * @throws \Exception
	 */
	function POST(){
		return $this->_GET();
	}
}
<?php
namespace Web\Page\Controller;

use Web\Page\Handler\HTMLPageBase;
use Utility\Image\Graph\Source\IGraphSource;
use Web\Page\Handler;
use Utility\Image\Graph\Renderer;

abstract class PaypalBase extends HTMLPageBase {
	private $action = 'process';
	
	abstract function getPaypalAccount();
	abstract function middleProcess(\Utility\Net\External\Paypal $p);
	abstract function onSuccess(\Utility\Net\External\Paypal $p);
	function onCancel(\Utility\Net\External\Paypal $p){
		echo "<html><head><title>Canceled</title></head><body><h3>The order was canceled.</h3>";
		echo "</body></html>";
	}
	abstract function onIPL(\Utility\Net\External\Paypal $p);
	
	function __construct($data){		
		// if there is not action variable, set the default action of 'process'
		if (!empty ( $data ['action'] ))
			$this->action = $data['action'];
	}
	
	/**
	 * Handle GET request
	 *
	 * @throws \Exception
	 */
	function GET(){
		$p = new \Utility\Net\External\Paypal (); // initiate an instance of the class

		$this_script = \Utility\Net\URL::fromRequest();
		$this_script->getPath()->setQuery(array());
		$this_script = (string)$this_script;

		switch ($this->action) {
		
			case 'process' : // Process and order...		
				
				$p->add_field ( 'business', $this->getPaypalAccount() );
				$p->add_field ( 'return', $this_script . '?action=success' );
				$p->add_field ( 'cancel_return', $this_script . '?action=cancel' );
				$p->add_field ( 'notify_url', $this_script . '?action=ipn' );
				
				$this->middleProcess($p);
		
				$p->submit_paypal_post ();
				break;
		
			case 'success' : // Order was successful...
				return $this->onSuccess($p);
		
			case 'cancel' : // Order was canceled...
				return $this->onCancel($p);
		
			case 'ipn' : // Paypal is calling page for IPN validation...
				return $this->onIPL($p);
		}
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
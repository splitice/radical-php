<?php
namespace Web\Pages;
use Image\Graph\Source\IGraphSource;
use Web\PageHandler;
use Image\Graph\Renderer;

abstract class PaypalBase extends PageHandler\HTMLPageBase {
	private $action = 'process';
	
	abstract function getPaypalAccount();
	abstract function middleProcess(\Net\ExternalInterfaces\Paypal $p);
	abstract function onSuccess(\Net\ExternalInterfaces\Paypal $p);
	function onCancel(\Net\ExternalInterfaces\Paypal $p){
		echo "<html><head><title>Canceled</title></head><body><h3>The order was canceled.</h3>";
		echo "</body></html>";
	}
	abstract function onIPL(\Net\ExternalInterfaces\Paypal $p);
	
	function __construct($data){		
		// if there is not action variable, set the default action of 'process'
		if (!empty ( $data ['action'] ))
			$this->action = $data['action'];
	}
	function GET(){
		$p = new \Net\ExternalInterfaces\Paypal (); // initiate an instance of the class

		$this_script = \Net\URL::fromRequest();
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
	function POST(){
		return $this->GET();
	}
}
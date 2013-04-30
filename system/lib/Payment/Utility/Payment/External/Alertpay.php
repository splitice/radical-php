<?php
namespace Utility\Payment\External;

use Utility\Payment\Logging;

class Alertpay {
	const IPN = "https://secure.payza.com/ipn2.ashx";
	const IPN_SANDBOX = 'https://sandbox.Payza.com/sandbox/IPN2.ashx';
	
	const URL = 'https://secure.payza.com/PayProcess.aspx';
	const URL_SANDBOX = 'https://sandbox.Payza.com/sandbox/payprocess.aspx';
	
	public $url = self::URL;
	public $ipn = self::IPN;
	protected $field = array();
	public $ipn_data;
		
	function __construct() {		
		// populate $fields array with a few default values.  See the paypal
		// documentation for a list of fields and their data types. These defaul
		// values can be overwritten by the calling script.
		

		$this->add_field ( 'ap_purchasetype', 'Item' ); // Return method = POST
		$this->add_field ( 'ap_currency', 'USD' );
	
	}
	
	function add_field($field, $value) {
		
		// adds a key=>value pair to the fields array, which is what will be 
		// sent to paypal as POST variables.  If the value is already in the 
		// array, it will be overwritten.
		

		$this->fields [$field] = $value;
	}
	
	function submit() {
		
		// this function actually generates an entire HTML page consisting of
		// a form with hidden elements which is submitted to paypal via the 
		// BODY element's onLoad attribute.  We do this so that you can validate
		// any POST vars from you custom form before submitting to paypal.  So 
		// basically, you'll have your own form which is submitted to your script
		// to validate the data, which in turn calls this function to create
		// another hidden form and submit to paypal.
		

		// The user will briefly see a message on the screen that reads:
		// "Please wait, your order is being processed..." and then immediately
		// is redirected to paypal.
		

		echo "<html>\n";
		echo "<head><title>Processing Payment...</title></head>\n";
		echo "<body onLoad=\"document.forms['alertpay_form'].submit();\">\n";
		echo "<center><h2>Please wait, your order is being processed and you";
		echo " will be redirected to the alertpay website.</h2></center>\n";
		echo "<form method=\"post\" name=\"alertpay_form\" ";
		echo "action=\"" . $this->url . "\">\n";
		
		foreach ( $this->fields as $name => $value ) {
			echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
		}
		echo "<center><br/><br/>If you are not automatically redirected to ";
		echo "alertpay within 5 seconds...<br/><br/>\n";
		echo "<input type=\"submit\" value=\"Click Here\"></center>\n";
		
		echo "</form>\n";
		echo "</body></html>\n";
	
	}
	
	function validate_ipn($security_code) {
		$log = new Logging('Alertpay');
		
		//The value is the url address of IPN V2 handler and the identifier of the token string
		
		// get the token from Payza
		$token = urlencode($_POST['token']);
		
		if($_POST['ap_securitycode'] == $security_code){
			$this->ipn_data = $_POST;
			return true;
		}
		return false;
	}
	
	function log_ipn_results($success) {
		if (! $this->ipn_log)
			return; // is logging turned off?
		

		// Timestamp
		$text = '[' . date ( 'm/d/Y g:i A' ) . '] - ';
		
		// Success or failure being logged?
		if ($success)
			$text .= "SUCCESS!\n";
		else
			$text .= 'FAIL: ' . $this->last_error . "\n";
		
		// Log the POST variables
		$text .= "IPN POST Vars from Paypal:\n";
		foreach ( $this->ipn_data as $key => $value ) {
			$text .= "$key=$value, ";
		}
		
		// Log the response from the paypal server
		$text .= "\nIPN Response from Paypal Server:\n " . $this->ipn_response;
		
		$log = new Logging('Alertpay');
		$log->log($text);
	}
}         


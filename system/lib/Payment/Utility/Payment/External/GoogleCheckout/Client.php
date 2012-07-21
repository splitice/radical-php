<?php
namespace Utility\Payment\External\GoogleCheckout;
/**
 * This class is responsible for sending messages to Google Checkout. It
 * is utilized by GoogleCheckoutServer to issue instructions in response
 * to various events.
 *
 * @package GoogleCheckoutServer
 */
class Client {
	private $logger;
	function __construct() {
		$this->logger = &Log::factory('file', CHECKOUT_LOG_FILE , 'GOOGLE_CHECKOUT');
	}
	/**
	 * This method sends a message to the Google Checkout Server.
	 *
	 * @param string The raw XML you want to send to Google.
	 * @return string The raw XML that is returned by Google.
	 */
	function send($message) {
		$post_url = CHECKOUT_SERVER."cws/v2/Merchant/".MERCHANT_ID."/request";
		$this->logger->log("Sending request to $post_url.");
		$old_log_level = error_reporting(E_ERROR);
		$request =& new HTTP_Request($post_url);
		$request->setMethod(HTTP_REQUEST_METHOD_POST);
		$request->setBasicAuth(MERCHANT_ID, MERCHANT_KEY);
		$request->addHeader("Content-type","application/xml");
		$request->addHeader("Accept","application/xml");
		$request->addRawPostData($message);
		$response = $request->sendRequest();
		error_reporting($old_log_level);
		$this->logger->log("Request sent.");
		if (PEAR::isError($response)) {
			$this->logger->log("There was an error (".$request->getResponseCode()."): " . $response->getMessage());
		} else {
			$this->logger->log("Request successful: " . $request->getResponseBody());
			return $request->getResponseBody();
		}
	}
}
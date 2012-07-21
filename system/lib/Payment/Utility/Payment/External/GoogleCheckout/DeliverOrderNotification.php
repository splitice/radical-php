<?php

namespace Utility\Payment\External\GoogleCheckout;

/**
 * A message you send to Google asking them to mark the order as delivered.
 *
 * The <deliver-order> command instructs Google Checkout to update an order's
 * fulfillment state from either NEW or PROCESSING to DELIVERED. You would
 * send this command after the order has been charged and shipped.
 *
 * @package GoogleCheckoutServer
 */
class DeliverOrderNotification extends Message {
	protected $orderNumber;
	protected $sendEmail;
	/**
	 *
	 * @param
	 *        	string The order number.
	 * @param
	 *        	TrackingData Any additional tracking data you have for the
	 *        	order
	 * @param
	 *        	boolean Send an email to the user?
	 */
	function __construct($o, $td, $send) {
		$this->orderNumber = $o;
		$this->trackingData = $td;
		$this->sendEmail = $send;
	}
	/**
	 * Serialize the notification into XML.
	 * 
	 * @return string The message in XML.
	 */
	public function toXML() {
		$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= '<deliver-order xmlns="http://checkout.google.com/schema/2" google-order-number="' . $this->orderNumber . '">' . "\n";
		if ($this->trackingData) {
			$xml .= $this->trackingData->toXML ();
		}
		$xml .= '  <send-email>' . $this->sendEmail ? 'true' : 'false' . '</send-email>' . "\n";
		$xml .= '</deliver-order>' . "\n";
		return $xml;
	}
}
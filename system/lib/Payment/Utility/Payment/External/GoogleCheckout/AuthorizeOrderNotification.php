<?php
namespace Utility\Payment\External\GoogleCheckout;
/**
 * A message you send to Google asking them to re-authorize the purchaser's credit
 * card.
 *
 * The <authorize-order> command instructs Google Checkout to explicitly
 * reauthorize a customer's credit card for the uncharged balance of an order
 * to verify that funds for the order are available. You may issue an
 * <authorize-order> command for an order that is in either of the following
 * financial order states:
 * - CHARGEABLE
 * - CHARGED (You could reauthorize an order that had been partially charged.)
 *
 * @package GoogleCheckoutServer
 */
class AuthorizeOrderNotification extends Message {
	protected $orderNumber;
	/**
	 * @param string The Google Checkout order number.
	 */
	function __construct($o) {
		$this->orderNumber = $o;
	}
	/**
	 * Serialize the notification into XML.
	 * @return string The message formated as XML.
	 */
	public function toXML() {
		$xml  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$xml .= '<authorize-order xmlns="http://checkout.google.com/schema/2" google-order-number="'.$this->orderNumber.'" />'."\n";
		return $xml;
	}
}
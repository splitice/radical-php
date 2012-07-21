<?php

namespace Utility\Payment\External\GoogleCheckout;

/**
 * A message you send to Google asking them to charge the purchaser.
 *
 * The <charge-order> command instructs Google Checkout to charge the buyer
 * for a particular order. After an order reaches the CHARGEABLE order state,
 * you have seven days ? 168 hours ? to capture funds by issuing a
 * <charge-order> command. You may issue a <charge-order> command for an
 * order that is in any of the following financial order states:
 * - REVIEWING
 * - CHARGEABLE
 * - CHARGED (if the order has only been partially charged)
 *
 * @package GoogleCheckoutServer
 */
class ChargeOrderNotification extends Message {
	protected $orderNumber;
	protected $orderTotal;
	/**
	 *
	 * @param
	 *        	string The Google Checkout order number.
	 * @param
	 *        	float The total amount of the order.
	 */
	function __construct($o, $a) {
		$this->orderNumber = $o;
		$this->orderTotal = $a;
	}
	/**
	 * Serialize the notification into XML.
	 * 
	 * @return string The message formated as XML.
	 */
	public function toXML() {
		$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= '<charge-order xmlns="http://checkout.google.com/schema/2" google-order-number="' . $this->orderNumber . '">' . "\n";
		$xml .= '  <amount currency="USD">' . $this->orderTotal . '</amount>' . "\n";
		$xml .= '</charge-order>' . "\n";
		return $xml;
	}
}
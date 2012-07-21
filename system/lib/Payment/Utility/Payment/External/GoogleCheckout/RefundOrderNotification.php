<?php

namespace Utility\Payment\External\GoogleCheckout;

/**
 * A message you sent to Google asking them to refund the purchaser.
 *
 * The <refund-order> command instructs Google Checkout to refund the buyer
 * for a particular order. You may issue a <refund-order> command after an
 * order has been charged and is in the CHARGED financial order state.
 *
 * @package GoogleCheckoutServer
 */
class RefundOrderNotification extends Message {
	protected $orderNumber;
	protected $amount;
	protected $comment;
	protected $reason;
	/**
	 *
	 * @param
	 *        	string The Google Checkout order number.
	 * @param
	 *        	float The amount to refund.
	 * @param
	 *        	string A comment about the refund.
	 * @param
	 *        	string The reason for the refund.
	 */
	function __construct($o, $a, $c, $r) {
		$this->orderNumber = $o;
		$this->amount = $a;
		$this->comment = $c;
		$this->reason = $r;
	}
	/**
	 * Serialize the notification into XML.
	 * 
	 * @return string The message formated as XML.
	 */
	public function toXML() {
		$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= '<refund-order xmlns="http://checkout.google.com/schema/2" google-order-number="' . $this->orderNumber . '">' . "\n";
		$xml .= '  <amount currency="USD">' . $this->orderTotal . '</amount>' . "\n";
		$xml .= '  <comment>' . $this->comment . '</comment>' . "\n";
		$xml .= '  <reason>' . $this->reason . '</reason>' . "\n";
		$xml .= '</refund-order>' . "\n";
		return $xml;
	}
}
<?php
namespace Utility\Payment\External\GoogleCheckout;
/**
 * A message you send to Google instructing them to cancel the order.
 *
 * The <cancel-order> command instructs Google Checkout to cancel a
 * particular order. If the customer has already been charged, you must
 * refund the customer's money before you can cancel the order. You may
 * issue a <cancel-order> command for an order that is in either of the
 * following financial order states:
 * - CHARGEABLE
 * - PAYMENT_DECLINED
 *
 * @package GoogleCheckoutServer
 */
class CancelOrderNotification extends Message {
	protected $orderNumber;
	protected $comment;
	protected $reason;
	/**
	 * @param string The Google Checkout order number.
	 * @param string A comment about the canceled order.
	 * @param string The reason the order is being cancelled.
	 */
	function __construct($o,$c,$r) {
		$this->orderNumber = $o;
		$this->comment = $c;
		$this->reason = $r;
	}
	/**
	 * Serialize the notification into XML.
	 * @return string The message formated as XML.
	 */
	public function toXML() {
		$xml  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$xml .= '<cancel-order xmlns="http://checkout.google.com/schema/2" google-order-number="'.$this->orderNumber.'">'."\n";
		$xml .= '  <comment>'.$this->comment.'</comment>'."\n";
		$xml .= '  <reason>'.$this->reason.'</reason>'."\n";
		$xml .= '</cancel-order>'."\n";
		return $xml;
	}
}
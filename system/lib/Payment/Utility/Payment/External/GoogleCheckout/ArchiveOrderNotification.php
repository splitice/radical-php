<?php
namespace Utility\Payment\External\GoogleCheckout;
/**
 * A message you send to Google asking them to archive the order.
 *
 * The <archive-order> command instructs Google Checkout to remove an order
 * from your Merchant Center Inbox. We recommend that you only archive orders
 * after they have been delivered or canceled.
 *
 * @package GoogleCheckoutServer
 */
class ArchiveOrderNotification extends Message {
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
		$xml .= '<archive-order xmlns="http://checkout.google.com/schema/2" google-order-number="'.$this->orderNumber.'" />'."\n";
		return $xml;
	}
}
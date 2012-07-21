<?php

namespace Utility\Payment\External\GoogleCheckout;

/**
 * A message sent by Google to let you know they have refunded the purchaser.
 *
 * Google Checkout sends a <refund-amount-notification> after successfully
 * executing a <refund-order> order processing command. You should not assume
 * that
 * Google has granted a refund until you receive this notification. Typically,
 * this notification is sent within seconds of the corresponding <refund-order>
 * request.
 *
 * @package GoogleCheckoutServer
 */
class RefundAmountNotification extends Message {
	private $totalRefundAmount;
	/**
	 * Converts XML into a proper object.
	 * 
	 * @param
	 *        	XmlElement An XML data structure
	 * @return RefundAmountNotification
	 */
	public static function fromXML($d) {
		$m = new RefundAmountNotification ();
		$m->data = $d;
		for($i = 0; $i < count ( $d->children ); $i ++) {
			if ($d->children [$i]->name == "google-order-number") {
				$m->orderNumber = $d->children [$i]->content;
			} else if ($d->children [$i]->name == "timestamp") {
				$m->timestamp = $d->children [$i]->content;
			}
		}
		$m->totalRefundAmount = $d->children [2]->content;
		return $m;
	}
	/**
	 * Converts an array into a proper object.
	 * 
	 * @param
	 *        	Array An associative array, typically a $_POST array.
	 * @return RefundAmountNotification
	 */
	public static function fromPost($array) {
		$m = new RefundAmountNotification ();
		$m->serialNumber = $array ['serial-number'];
		$m->timestamp = $array ['timestamp'];
		$m->order = new Order ();
		$m->order->orderNumber = $array ['google-order-number'];
		$m->totalRefundAmount = $array ['total-refund-amount'];
		return $m;
	}
	/**
	 *
	 * @return float The amount refunded.
	 */
	public function totalRefundAmount() {
		return $this->totalRufundAmount;
	}
}
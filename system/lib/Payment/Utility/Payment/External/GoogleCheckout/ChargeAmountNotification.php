<?php

namespace Utility\Payment\External\GoogleCheckout;

/**
 * A message sent by Google letting you know they charged the user.
 *
 * Google Checkout sends a <charge-amount-notification> after successfully
 * charging
 * a customer for an order. Google Checkout will attempt to charge a customer if
 * you send a <charge-order> order processing command requesting a charge. You
 * can also instruct Google to automatically charge customers when they submit
 * new
 * orders. To set this preference, log in to your Checkout account and click the
 * Settings tab. Then click the Preferences link in the menu on the left side of
 * the page. Finally, select the option to "Automatically authorize and charge
 * the buyer's credit card."
 *
 * @package GoogleCheckoutServer
 */
class ChargeAmountNotification extends Message {
	private $latestChargeAmount;
	private $totalChargeAmount;
	/**
	 * Converts XML into a proper object.
	 * 
	 * @param
	 *        	XmlElement An XML data structure
	 * @return ChargeAmountNotification
	 * @todo verify
	 */
	public static function fromXML($d) {
		$m = new ChargeAmountNotification ();
		$m->data = $d;
		for($i = 0; $i < count ( $d->children ); $i ++) {
			if ($d->children [$i]->name == "google-order-number") {
				$m->orderNumber = $d->children [$i]->content;
			} else if ($d->children [$i]->name == "timestamp") {
				$m->timestamp = $d->children [$i]->content;
			}
		}
		$m->latestChargeAmount = $d->children [2]->content;
		$m->totalChargeAmount = $d->children [3]->content;
		return $m;
	}
	/**
	 * Converts an array into a proper object.
	 * 
	 * @param
	 *        	Array An associative array, typically a $_POST array.
	 * @return ChargeAmountNotification
	 */
	public static function fromPost($array) {
		$m = new ChargeAmountNotification ();
		$m->serialNumber = $array ['serial-number'];
		$m->timestamp = $array ['timestamp'];
		$m->order = new Order ();
		$m->order->orderNumber = $array ['google-order-number'];
		$m->latestChargeAmount = $array ['latest-charge-amount'];
		$m->totalChargeAmount = $array ['total-charge-amount'];
		return $m;
	}
	/**
	 *
	 * @return float The amount most recently charged for a particular order.
	 */
	function latestChargeAmount() {
		return $this->latestChargeAmount;
	}
	/**
	 *
	 * @return float The total amount charged for a particular order.
	 */
	function totalChargeAmount() {
		return $this->totalChargeAmount;
	}
}
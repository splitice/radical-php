<?php
namespace Utility\Payment\External\GoogleCheckout;
/**
 * A notification sent by Google when the state of an order changes.
 * @package GoogleCheckoutServer
 */
class OrderStateChangeNotification extends Message {
	/**
	 * Converts XML into a proper object.
	 * @param XmlMLElement An XML data structure
	 * @return OrderStateChangeNotification
	 */
	public static function fromXML($struct) {
		$m = new OrderStateChangeNotification();
		$m->data = $d;
		for ($i = 0; $i < count($d->children); $i++) {
			if ($d->children[$i]->name == "google-order-number") {
				$m->orderNumber = $d->children[$i]->content;
			} else if ($d->children[$i]->name == "timestamp") {
				$m->timestamp = $d->children[$i]->content;
			}
		}
		return $m;
	}
	/**
	 * Converts an array into a proper object.
	 * @param Array An associative array, typically a $_POST array.
	 * @return OrderStateChangeNotification
	 */
	public static function fromPost($array) {
		$m = new OrderStateChangeNotification();
		$m->serialNumber = $array['serial-number'];
		$m->timestamp = $array['timestamp'];
		$m->order = new Order();
		$m->order->orderNumber = $array['google-order-number'];
		$m->orderNumber = $m->order->orderNumber;
		$m->fulfillmentState = $array['new-fulfillment-order-state'];
		$m->financialState = $array['new-financial-order-state'];
		$m->previousFulfillmentState = $array['previous-fulfillment-order-state'];
		$m->previousFinancialState = $array['previous-financial-order-state'];
		return $m;
	}
	/**
	 * @return string The fullfilment state of the order.
	 */
	function fulfillmentState() { return $this->fulfillmentState; }
	/**
	 * @return string The financial state of the order.
	 */
	function financialState() { return $this->financialState; }
	/**
	 * @return string The <em>previous</em> fullfilment state of the order.
	 */
	function previousFulfillmentState() { return $this->previousFulfillmentState; }
	/**
	 * @return string The <em>previous</em> financial state of the order.
	 */
	function previousFinancialState() { return $this->previousFinancialState; }
}
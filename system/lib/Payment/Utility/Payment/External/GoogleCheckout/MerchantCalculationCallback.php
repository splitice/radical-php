<?php
namespace Utility\Payment\External\GoogleCheckout;
/**
 * A simple helper function to deserialize a call to the Merchant
 * Calculation callback.
 *
 * @package GoogleCheckoutServer
 * @todo extract serial number in fromXML
 */
class MerchantCalculationCallback extends Message {
	/**
	 * Converts XML into a proper object.
	 * @param XmlElement An XML data structure
	 * @return MerchantCalculationCallback
	 */
	public static function fromXML($struct) {
		$m = new MerchantCalculationCallback();
		$m->data = $d;
		for ($i = 0; $i < count($d->children); $i++) {
			if ($d->children[$i]->name == "google-order-number") {
				$m->orderNumber = $d->children[$i]->content;
			} else if ($d->children[$i]->name == "timestamp") {
				$m->timestamp = $d->children[$i]->content;
			}
		}
		$m->orderNumber = $m->order->orderNumber;
		return $m;

	}
	/**
	 * Converts an array into a proper object.
	 * @param Array An associative array, typically a $_POST array.
	 * @return MerchantCalculationCallback
	 */
	public static function fromPost($array) {
		$m = new MerchantCalculationCallback();
		$m->serialNumber = $array['serial-number'];
		$m->timestamp = $array['timestamp'];
		$m->order = new Order();
		$m->order->orderNumber = $array['google-order-number'];
		$m->orderNumber = $m->order->orderNumber;
		return $m;
	}
}
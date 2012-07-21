<?php

namespace Utility\Payment\External\GoogleCheckout;

/**
 * The element encapsulates verbatim the shopping cart as it was submitted to
 * Google from the seller's web site.
 *
 * @package GoogleCheckoutServer
 */
class ShoppingCartElement {
	/**
	 * An array of private data passed to Google Checkout from the seller.
	 * 
	 * @var Array
	 */
	public $privateData = array ();
	/**
	 * Converts XML into a proper object.
	 * 
	 * @param
	 *        	XMLElement An XML data structure
	 * @return ShoppingCartElement
	 */
	public static function fromXML($d) {
		$m = new ShoppingCartElement ();
		for($i = 0; $i < count ( $d->children ); $i ++) {
			if ($d->children [$i]->name == "google-order-number") {
				$m->orderNumber = $d->children [$i]->content;
			} else if ($d->children [$i]->name == "timestamp") {
				$m->timestamp = $d->children [$i]->content;
			}
			if ($struct->children [$i]->name == "merchant-private-data") {
				for($j = 0; $j < count ( $d->children [$i]->children ); $j ++) {
					$k = $d->children [$i]->children [$j]->name;
					$v = $d->children [$i]->children [$j]->content;
					$this->privateData [$k] = $v;
				}
			}
		}
	}
	/**
	 * Converts an array into a proper object.
	 * 
	 * @param
	 *        	Array An associative array, typically a $_POST array.
	 * @return ShoppingCartElement
	 */
	public static function fromPost($array) {
		/* TODO - verify */
		$m = new ShoppingCartElement ();
		$m->privateData = $array ['shopping-cart.merchant-private-data'];
		return $m;
	}
}
<?php

namespace Utility\Payment\External\GoogleCheckout;

/**
 * A message sent by Google letting you know of a new order placed by a
 * purchaser.
 *
 * Google sends a new order notification when a buyer places an order through
 * Google Checkout. This notification contains the following information:
 * - the shopping cart as Google received it
 * - order-adjustment, taxes, coupons, gift certificates, etc
 * - buyer billing and shipping address
 * - order total
 * - buyer id
 *
 * @package GoogleCheckoutServer
 */
class NewOrderNotification extends Message {
	/**
	 * An XML struct of the the entire message.
	 * 
	 * @var XmlElement
	 */
	public $data;
	/**
	 * The shopping cart as it was posted to Google.
	 * 
	 * @var ShoppingCartElement
	 */
	public $shoppingCart;
	/**
	 * The purchaser's marketing preferences, e.g.
	 * can you contact them via email?
	 * 
	 * @var MarketingPreferences
	 */
	public $buyerMarketingPreferences;
	/**
	 * The purchaser's order.
	 * 
	 * @var Order
	 */
	public $order;
	/**
	 * Converts XML into a proper object.
	 * 
	 * @param
	 *        	XmlElement An XML data structure
	 * @return NewOrderNotification
	 */
	public static function fromXML($d) {
		$this->data = $d;
		for($i = 0; $i < count ( $d->children ); $i ++) {
			if ($d->children [$i]->name == "google-order-number") {
				$this->orderNumber = $d->children [$i]->content;
			} else if ($d->children [$i]->name == "timestamp") {
				$this->timestamp = $d->children [$i]->content;
			}
		}
		$this->order = new Order ();
		$this->order->orderNumber = $this->orderNumber ();
		for($i = 0; $i < count ( $d->children ); $i ++) {
			if ($d->children [$i]->name == "buyer-shipping-address") {
				$this->order->shippingAddress = Address::fromXML ( $d->children [$i] );
			} else if ($d->children [$i]->name == "buyer-billing-address") {
				$this->order->billingAddress = Address::fromXML ( $d->children [$i] );
			} else if ($d->children [$i]->name == "buyer-marketing-preferences") {
				$this->buyerMarketingPreferences = new MarketingPreferences ( $d->children [$i] );
			} else if ($d->children [$i]->name == "order-total") {
				$this->order->orderTotal = $d->children [$i]->content;
			} else if ($d->children [$i]->name == "fulfillment-order-state") {
				$this->order->fulfillmentState = $d->children [$i]->content;
			} else if ($d->children [$i]->name == "financial-order-state") {
				$this->order->financialState = $d->children [$i]->content;
			} else if ($d->children [$i]->name == "buyer-id") {
				$this->order->buyerId = $d->children [$i]->content;
			} else if ($d->children [$i]->name == "shopping-cart") {
				$this->shoppingCart = new ShoppingCartElement ( $d->children [$i] );
			}
		}
	}
	
	/**
	 * Converts an array into a proper object.
	 * 
	 * @param
	 *        	Array An associative array, typically a $_POST array.
	 * @return NewOrderNotification
	 */
	public static function fromPost($array) {
		$m = new NewOrderNotification ();
		$m->serialNumber = $array ['serial-number'];
		$m->timestamp = $array ['timestamp'];
		$m->orderNumber = $array ['google-order-number'];
		$m->order = new Order ();
		$m->order->orderNumber = $m->orderNumber;
		$m->order->orderTotal = $array ['order-total'];
		$m->order->fulfillmentState = $array ['fulfillment-order-state'];
		$m->order->financialState = $array ['financial-order-state'];
		$m->order->buyerId = $array ['buyer-id'];
		$m->order->shippingAddress = Address::fromPost ( $array, 'buyer-shipping-address' );
		$m->order->billingAddress = Address::fromPost ( $array, 'buyer-billing-address' );
		$m->buyerMarketingPreferences = MarketingPreferences::fromPost ( $array );
		$m->shoppingCart = ShoppingCartElement::fromPost ( $array );
		return $m;
	}
	
	/**
	 *
	 * @return float The order's total amount.
	 */
	function orderTotal() {
		return $this->order->orderTotal;
	}
	/**
	 *
	 * @return string The order's current fulfillment state.
	 */
	function fulfillmentState() {
		return $this->order->fulfillmentState;
	}
	/**
	 *
	 * @return string The order's current financial state.
	 */
	function financialState() {
		return $this->order->financialState;
	}
	/**
	 *
	 * @return int The buyer Id of the purchaser.
	 */
	function buyerId() {
		return $this->order->buyerId;
	}
}
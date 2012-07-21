<?php

namespace Utility\Payment\External\GoogleCheckout;

/**
 * This the base class for all messages received from Google Checkout.
 * It
 * contains the data that is found most commonly among those messages.
 *
 * @package GoogleCheckoutServer
 */
class Message {
	protected $data;
	protected $orderNumber;
	protected $timestamp;
	protected $serialNumber;
	/**
	 * The serial number contained within the message.
	 * 
	 * @return string The serial number.
	 */
	function serialNumber() {
		return $this->serialNumber;
	}
	/**
	 * The timestamp contained within the message.
	 * 
	 * @return timestamp The timestamp in Unix epoch.
	 */
	function timestamp() {
		return $this->timestamp;
	}
	/**
	 * The Google Checkout Order Number contained within the message.
	 * 
	 * @return string The order number.
	 */
	function orderNumber() {
		return $this->orderNumber;
	}
}
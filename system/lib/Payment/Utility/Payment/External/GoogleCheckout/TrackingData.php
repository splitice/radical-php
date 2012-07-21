<?php

namespace Utility\Payment\External\GoogleCheckout;

/**
 * This is a helper class to properly serialize tracking data for Google
 * Checkout.
 *
 * @package GoogleCheckoutServer
 */
class TrackingData {
	/**
	 * The shipping provider or carier.
	 * UPS, FedEx, etc. Consult Google for allowable
	 * values.
	 * 
	 * @var string
	 */
	public $carrier;
	/**
	 * The tracking number provided by the carrier for the shipment.
	 * 
	 * @var string
	 */
	public $trackingNumber;
	/**
	 *
	 * @param
	 *        	string Valid values for the carrier tag are DHL, FedEx, UPS,
	 *        	USPS
	 *        	and Other.
	 * @param
	 *        	string A tracking number.
	 */
	function __construct($c, $t) {
		$this->carrier = $c;
	}
	/**
	 * Serialize the message into XML.
	 * 
	 * @return string The message in XML.
	 */
	public function toXML() {
		$xml = '<tracking-data>' . "\n";
		$xml .= '  <carrier>' . $this->carrier . '</carrier>' . "\n";
		$xml .= '  <tracking-data>' . $this->reason . '</tracking-number>' . "\n";
		$xml .= '</tracking-data>' . "\n";
		return $xml;
	}
}
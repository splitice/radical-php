<?php

namespace Utility\Payment\External\GoogleCheckout;

/**
 * A message sent by Google letting you know critical information about the
 * purchaser.
 *
 * Google Checkout sends a risk information notification after completing its
 * risk analysis on a new order. A risk-information-notification includes
 * financial information such as the customer's billing address, a partial
 * credit card number and other values that help you verify that an order is
 * not fraudulent.
 *
 * @package GoogleCheckoutServer
 */
class RiskInformationNotification extends Message {
	/**
	 * The buyer's billing address.
	 * Of type Address.
	 * 
	 * @var Address
	 */
	public $buyerBillingAddress;
	/**
	 * Converts XML into a proper object.
	 * 
	 * @param
	 *        	XmlElement An XML data structure
	 * @return RiskInformationNotification
	 */
	public static function fromXML($d) {
		$m = new RiskInformationNotification ();
		$m->data = $d;
		for($i = 0; $i < count ( $d->children ); $i ++) {
			if ($d->children [$i]->name == "google-order-number") {
				$m->orderNumber = $d->children [$i]->content;
			} else if ($d->children [$i]->name == "timestamp") {
				$m->timestamp = $d->children [$i]->content;
			}
		}
		$m->buyerBillingAddress = Address::fromXML ( $d->children [2]->children [0] );
		$m->ipAddress = $d->children [2]->children [1]->content;
		$m->eligibleForProtection = $d->children [2]->children [2]->content;
		$m->avsResponse = $d->children [2]->children [3]->content;
		$m->cvnResponse = $d->children [2]->children [4]->content;
		$m->partialCCNumber = $d->children [2]->children [5]->content;
		$m->buyerAccountAge = $d->children [2]->children [6]->content;
		return $m;
	}
	/**
	 * Converts an array into a proper object.
	 * 
	 * @param
	 *        	Array An associative array, typically a $_POST array.
	 * @return RiskInformationNotification
	 */
	public static function fromPost($array) {
		$m = new RiskInformationNotification ();
		$m->serialNumber = $array ['serial-number'];
		$m->timestamp = $array ['timestamp'];
		$m->orderNumber = $array ['google-order-number'];
		$m->order = new Order ();
		$m->order->orderNumber = $m->order->orderNumber;
		$m->buyerBillingAddress = Address::fromPost ( $array, 'risk-information_billing-address' );
		$m->ipAddress = $array ['risk-information_ip-address'];
		$m->eligibleForProtection = $array ['risk-information_eligible-for-protection'];
		$m->avsResponse = $array ['risk-information_avs-response'];
		$m->cvnResponse = $array ['risk-information_cvn-response'];
		$m->partialCCNumber = $array ['risk-information_partial-cc-number'];
		$m->buyerAccountAge = $array ['risk-information_buyer-account-age'];
		return $m;
	}
	/**
	 *
	 * @return string The IP address of the purchaser.
	 */
	function ipAddress() {
		return $this->ipAddress;
	}
	/**
	 *
	 * @return boolean Whether the purchaser is eligible for protection.
	 */
	function eligibleForProtection() {
		return $this->eligibleForProtection;
	}
	/**
	 *
	 * @return string Response from the Address Verification System.
	 */
	function avsResponse() {
		return $this->avsResponse;
	}
	/**
	 * Possible values for this tag are:
	 * M - CVN match
	 * N - No CVN match
	 * U - CVN not available
	 * E - CVN error
	 *
	 * @return string The credit verification value for the order.
	 */
	function cvnResponse() {
		return $this->cvnResponse;
	}
	/**
	 *
	 * @return string The last four digits of the purchaser's credit card.
	 */
	function partialCCNumber() {
		return $this->partialCCNumber;
	}
	/**
	 *
	 * @return string The age, in days, of the buyer's Google Checkout account.
	 */
	function buyerAccountAge() {
		return $this->buyerAccountAge;
	}
}
<?php

namespace Utility\Payment\External\GoogleCheckout;

/**
 * Details about any adjustments made to an order.
 *
 * @package GoogleCheckoutServer
 */
class OrderAdjustment {
	/**
	 * The merchant codes.
	 * 
	 * @todo Define what merchant codes are.
	 * @var string
	 */
	public $merchantCodes;
	/**
	 * The total tax applied to the order.
	 * 
	 * @var float
	 */
	public $totalTax;
	/**
	 * The amount the order has been adjusted.
	 * 
	 * @var float
	 */
	public $adjustmentTotal;
	/**
	 * Converts XML into a proper object.
	 * 
	 * @param
	 *        	XmlElement An XML data structure
	 * @return OrderAdjustment
	 */
	public static function fromXML($struct) {
		$m = new OrderAdjustment ();
		$m->emailAllowed = $struct->children [0]->content;
		$m->totalTax = $struct->children [1]->content;
		$m->adjustmentTotal = $struct->children [2]->content;
		return $m;
	}
	/**
	 * Converts an array into a proper object.
	 * 
	 * @param
	 *        	Array An associative array, typically a $_POST array.
	 * @return OrderAdjustment
	 */
	public static function fromPost($array) {
		$m = new OrderAdjustment ();
		$m->emailAllowed = $array ['buyer-marketing-preferences_email-allowed'];
		$m->totalTax = $array ['order-adjustment_total-tax'];
		$m->adjustmentTotal = $array ['order-adjustment_adjustment-total'];
	}
}
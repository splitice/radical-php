<?php

namespace Utility\Payment\External\GoogleCheckout;

/**
 * The purchaser's marketing preferences, e.g.
 * can you send them email?
 *
 * @package GoogleCheckoutServer
 */
class MarketingPreferences {
	/**
	 * Whether or not this user may be contacted via email.
	 * 
	 * @var boolean
	 */
	public $emailAllowed;
	/**
	 * Converts XML into a proper object.
	 * 
	 * @param
	 *        	XmlElement An XML data structure
	 * @return MarketingPreferences
	 * @todo fix
	 */
	public static function fromXML($struct) {
		$this->emailAllowed = $struct->children [0]->content;
	}
	/**
	 * Converts an array into a proper object.
	 * 
	 * @param
	 *        	Array An associative array, typically a $_POST array.
	 * @return MarketingPreferences
	 */
	public static function fromPost($array) {
		$m = new MarketingPreferences ();
		$m->emailAllowed = $array ['buyer-marketing-preferences_email-allowed'];
		return $m;
	}
}
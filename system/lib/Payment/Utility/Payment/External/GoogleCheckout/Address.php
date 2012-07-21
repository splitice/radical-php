<?php

namespace Utility\Payment\External\GoogleCheckout;

/**
 * An address is a class that embodies physical snail mail address.
 *
 * Not only is this class a helper class to marshal either an XML struct or an
 * HTTP POST into a simple PHP data structure, but it also gives you access to
 * the
 * components of the address for billing and shipping purposes.
 *
 * @package GoogleCheckoutServer
 */
class Address {
	/**
	 * The purchaser's email address.
	 * 
	 * @var string
	 */
	public $email;
	/**
	 * The purchaser's address (line 1).
	 * 
	 * @var string
	 */
	public $address1;
	/**
	 * The purchaser's address (line 2).
	 * 
	 * @var string
	 */
	public $address2;
	/**
	 * The purchaser's company name (if any).
	 * 
	 * @var string
	 */
	public $companyName;
	/**
	 * The purchaser's name, or a contact name.
	 * 
	 * @var string
	 */
	public $contactName;
	/**
	 * The purchaser's phone number.
	 * 
	 * @var string
	 */
	public $phone;
	/**
	 * The purchaser's fax number (if any).
	 * 
	 * @var string
	 */
	public $fax;
	/**
	 * The country code for the purchaser.
	 * 
	 * @var string
	 */
	public $countryCode;
	/**
	 * The purchaser's city.
	 * 
	 * @var string
	 */
	public $city;
	/**
	 * The purchasers region or state.
	 * 
	 * @var string
	 */
	public $region;
	/**
	 * The postal, or zip code of the purchaser.
	 * 
	 * @var string
	 */
	public $postalCode;
	
	/**
	 * Converts an array into a proper object.
	 *
	 * @param
	 *        	Array An associative array, typically a $_POST array.
	 * @param
	 *        	string A prefix that proceeds each of the standard element
	 *        	names
	 * @return OrderAdjustment
	 */
	public static function fromPost($array, $pre) {
		$a = new Address ();
		$a->email = $array [$pre . "_email"];
		$a->address1 = $array [$pre . "_address1"];
		$a->address2 = $array [$pre . "_address2"];
		$a->companyName = $array [$pre . "_company-name"];
		$a->contactName = $array [$pre . "_contact-name"];
		$a->phone = $array [$pre . "_phone"];
		$a->fax = $array [$pre . "_fax"];
		$a->countryCode = $array [$pre . "_country-code"];
		$a->city = $array [$pre . "_city"];
		$a->region = $array [$pre . "_region"];
		$a->postalCode = $array [$pre . "_postal-code"];
		return $a;
	}
	
	/**
	 * Converts XML into a proper object.
	 * 
	 * @param
	 *        	XmlElement An XML data structure
	 * @return OrderAdjustment
	 */
	public static function fromXML($struct) {
		$a = new Address ();
		$a->email = $struct->children [0]->content;
		$a->address1 = $struct->children [1]->content;
		$a->address2 = $struct->children [2]->content;
		$a->companyName = $struct->children [3]->content;
		$a->contactName = $struct->children [4]->content;
		$a->phone = $struct->children [5]->content;
		$a->fax = $struct->children [6]->content;
		$a->countryCode = $struct->children [7]->content;
		$a->city = $struct->children [8]->content;
		$a->region = $struct->children [9]->content;
		$a->postalCode = $struct->children [10]->content;
		return $a;
	}
}
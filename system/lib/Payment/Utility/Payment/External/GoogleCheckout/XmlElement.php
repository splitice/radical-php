<?php

namespace Utility\Payment\External\GoogleCheckout;

/**
 * A helper class for parsing XML messages posted by Google Checkout.
 *
 * @package GoogleCheckoutServer
 */
class XmlElement {
	var $name;
	var $attributes;
	var $content;
	var $children;
}
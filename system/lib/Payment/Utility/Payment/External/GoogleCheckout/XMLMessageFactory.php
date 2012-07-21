<?php
namespace Utility\Payment\External\GoogleCheckout;
/**
 * This class is responsible for parsing the XML posted by Google and then
 * to construct and  return a valid GoogleMessage will all of the
 * posted content deserialized properly.
 *
 * Users do not need to interact with this class directly. The
 * GoogleCheckoutServer will interface with the proper message factory for
 * you automatically.
 *
 * @package GoogleCheckoutServer
 */
class XMLMessageFactory {
	/**
	 * This method takes as input raw XML and then returns the proper serialized
	 * object corresponding to the input.
	 * @param string The raw XML that was posted by Google.
	 * @return GoogleMessage A message corresponding to the posted
	 * content
	 * @throws Exception Throws an exception if the message posted in not
	 * recognized.
	 */
	public static function create($xml) {
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, $xml, $tags);
		xml_parser_free($parser);

		$elements = array();  // the currently filling [child] XmlElement array
		$stack = array();
		foreach ($tags as $tag) {
			$index = count($elements);
			if ($tag['type'] == "complete" || $tag['type'] == "open") {
				$elements[$index] = new XmlElement;
				$elements[$index]->name = $tag['tag'];
				$elements[$index]->attributes = $tag['attributes'];
				$elements[$index]->content = $tag['value'];
				if ($tag['type'] == "open") {  // push
					$elements[$index]->children = array();
					$stack[count($stack)] = &$elements;
					$elements = &$elements[$index]->children;
				}
			}
			if ($tag['type'] == "close") {  // pop
				$elements = &$stack[count($stack) - 1];
				unset($stack[count($stack) - 1]);
			}
		}
		$root = $elements[0];  // the single top-level element
		if ($root->name == 'new-order-notification') {
			return new NewOrderNotification($root);
		} else if ($root->name == 'order-state-change-notification') {
			return new OrderStateChangeNotification($root);
		} else if ($root->name == 'risk-information-notification') {
			return new RiskInformationNotification($root);
		} else if ($root->name == 'charge-amount-notification') {
			return new ChargeAmountNotification($root);
		} else if ($root->name == 'merchant-calculation-callback') {
			return new MerchantCalculationCallback($root);
		} else {
			throw new \Exception("Unknown message type: " . $root->name);
		}
	}
}
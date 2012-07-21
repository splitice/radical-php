<?php
namespace Utility\Payment\External\GoogleCheckout;
/**
 * @todo document
 * @package GoogleCheckoutServer
 */
class MerchantCalculationResults extends Message {
	private $totalTax;
	private $shippingRate;
	private $shippable;
	private $discountXML;
	private $giftXML;
	/**
	 * @param string The Google Checkout order number.
	 * @param float The total order amount.
	 */
	function __construct($o,$a) {
		$this->orderNumber = $o;
		$this->orderTotal = $a;
	}
	function processDiscount($code,$amount,$message) {
		$xml  = "<coupon-result>\n";
		$xml .= "  <valid>true</valid>\n";
		$xml .= "  <code>$code</code>\n";
		$xml .= "  <calculated-amount currency=\"USD\">$amount</calculated-amount>\n";
		$xml .= "  <message>$message</message>\n";
		$xml .= "</coupon-result>\n";
		$this->discountXML = $xml;
	}
	function rejectDiscount($code,$message) {
		$xml  = "<coupon-result>\n";
		$xml .= "  <valid>false</valid>\n";
		$xml .= "  <code>$code</code>\n";
		$xml .= "  <calculated-amount currency=\"USD\">0.00</calculated-amount>\n";
		$xml .= "  <message>$message</message>\n";
		$xml .= "</coupon-result>\n";
		$this->discountXML = $xml;
	}
	function processGiftCertificate($code,$amount,$message) {
		$xml  = "<gift-certificate-result>\n";
		$xml .= "  <valid>true</valid>\n";
		$xml .= "  <code>$code</code>\n";
		$xml .= "  <calculated-amount currency=\"USD\">$amount</calculated-amount>\n";
		$xml .= "  <message>$message</message>\n";
		$xml .= "</gift-certificate-result>\n";
		$this->giftXML = $xml;
	}
	function rejectGiftCertificate($code,$message) {
		$xml  = "<gift-certificate-result>\n";
		$xml .= "  <valid>false</valid>\n";
		$xml .= "  <code>$code</code>\n";
		$xml .= "  <calculated-amount currency=\"USD\">0.00</calculated-amount>\n";
		$xml .= "  <message>$message</message>\n";
		$xml .= "</gift-certificate-result>\n";
		$this->giftXML = $xml;
	}
	/**
	 * Serialize the notification into XML.
	 * @return string The message formated as XML.
	 */
	public function toXML() {
		$xml  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$xml .= '<merchant-calculation-results xmlns="http://checkout.google.com/schema/2">'."\n";
		$xml .= "  <results>\n";
		/*
		 $xml .= "    <result shipping-name=\"SuperShip\" address-id=\"739030698069958\">
		<shipping-rate currency="USD">7.03</shipping-rate>
		<shippable>true</shippable>

		<total-tax currency="USD">14.67</total-tax>
		<merchant-code-results>
		<coupon-result>
		<valid>true</valid>

		<code>FirstVisitCoupon</code>
		<calculated-amount currency="USD">5.00</calculated-amount>
		<message>Congratulations! You saved $5.00 on your first visit!</message>
		</coupon-result>

		<gift-certificate-result>
		<valid>true</valid>
		<code>GiftCert012345</code>
		<calculated-amount currency="USD">10.00</calculated-amount>

		<message>You used your Gift Certificate!</message>
		</gift-certificate-result>
		</merchant-code-results>
		</result>
		<result shipping-name="UPS Ground" address-id="739030698069958">

		<shipping-rate currency="USD">5.56</shipping-rate>
		<shippable>true</shippable>
		<total-tax currency="USD">14.67</total-tax>

		<merchant-code-results>
		<coupon-result>
		<valid>true</valid>
		<code>FirstVisitCoupon</code>
		<calculated-amount currency="USD">5.00</calculated-amount>

		<message>Congratulations! You saved $5.00 on your first visit!</message>
		</coupon-result>
		<gift-certificate-result>
		<valid>true</valid>
		<code>GiftCert012345</code>

		<calculated-amount currency="USD">10.00</calculated-amount>
		<message>You used your Gift Certificate!</message>
		</gift-certificate-result>
		</merchant-code-results>

		</result>
		<result shipping-name="SuperShip" address-id="421273450774618">
		<shipping-rate currency="USD">9.66</shipping-rate>
		<shippable>true</shippable>

		<total-tax currency="USD">17.57</total-tax>
		<merchant-code-results>
		<coupon-result>
		<valid>true</valid>

		<code>FirstVisitCoupon</code>
		<calculated-amount currency="USD">5.00</calculated-amount>
		<message>Congratulations! You saved $5.00 on your first visit!</message>
		</coupon-result>

		<gift-certificate-result>
		<valid>true</valid>
		<code>GiftCert012345</code>
		<calculated-amount currency="USD">10.00</calculated-amount>

		<message>You used your Gift Certificate!</message>
		</gift-certificate-result>
		</merchant-code-results>
		</result>
		<result shipping-name="UPS Ground" address-id="421273450774618">

		<shipping-rate currency="USD">7.68</shipping-rate>
		<shippable>true</shippable>
		<total-tax currency="USD">17.57</total-tax>

		<merchant-code-results>
		<coupon-result>
		<valid>true</valid>
		<code>FirstVisitCoupon</code>
		<calculated-amount currency="USD">5.00</calculated-amount>

		<message>Congratulations! You saved $5.00 on your first visit!</message>
		</coupon-result>
		<gift-certificate-result>
		<valid>true</valid>
		<code>GiftCert012345</code>

		<calculated-amount currency="USD">10.00</calculated-amount>
		<message>You used your Gift Certificate!</message>
		</gift-certificate-result>
		</merchant-code-results>

		</result>
		*/
		$xml .= "  </results>\n";
		$xml .= "</merchant-calculation-results>\n";
		return $xml;
	}
}
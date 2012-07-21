<?php

namespace Utility\Payment\External\GoogleCheckout;

/**
 * This class inspects the global $_POST variable, a variable managed by PHP
 * that contains all of the posted name value pairs, and constructs and
 * returns a valid GoogleMessage with all of the posted content
 * deserialized properly.
 *
 * Users do not need to interact with this class directly. The
 * GoogleCheckoutServer will interface with the proper message factory for
 * you automatically.
 *
 * @package GoogleCheckoutServer
 */
class HTMLMessageFactory {
	/**
	 *
	 * @return GoogleMessage A message corresponding to the posted
	 *         content
	 * @throws Exception Throws an exception if the message posted in not
	 *         recognized.
	 */
	public static function create() {
		global $_POST;
		if ($_POST ['_type'] == 'new-order-notification') {
			return NewOrderNotification::fromPost ( $_POST );
		} else if ($_POST ['_type'] == 'order-state-change-notification') {
			return OrderStateChangeNotification::fromPost ( $_POST );
		} else if ($_POST ['_type'] == 'risk-information-notification') {
			return RiskInformationNotification::fromPost ( $_POST );
		} else if ($_POST ['_type'] == 'charge-amount-notification') {
			return ChargeAmountNotification::fromPost ( $_POST );
		} else if ($_POST ['_type'] == 'merchant-calculation-callback') {
			return MerchantCalculationCallback::fromPost ( $_POST );
		} else {
			throw new \Exception ( "Unknown message type: " . $_POST ['_type'] );
		}
	}
}
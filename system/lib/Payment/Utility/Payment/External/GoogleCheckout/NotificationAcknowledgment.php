<?php

namespace Utility\Payment\External\GoogleCheckout;

/**
 * A message you send to Google letting them know you have successfully
 * received a notification.
 *
 * This message signals to Google that your system has successfully received
 * and processed any notification sent to you via the Notification API. This
 * message must be sent synchronously in response to the notification you
 * receive.
 *
 * @package GoogleCheckoutServer
 */
class NotificationAcknowledgment extends Message {
	function __construct() {
	}
	/**
	 * Serialize the notification into XML.
	 * 
	 * @return string The message formated as XML.
	 */
	function toXML() {
		$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$xml .= '<notification-acknowledgment xmlns="http://checkout.google.com/schema/2" />';
		return $xml;
	}
}
<?php
namespace Utility\Payment\External\GoogleCheckout;
/**
 * The Google Checkout Server Package
 * 
 * This package contains all of the classes necessary to respond to 
 * notifications sent by Google to your purchasing system. This system is 
 * intended as a base class for your own implementation and business logic. 
 * This set of base classes marshalls the request into a set of objects, 
 * and prepares a response for you to modify.
 *
 * This library is free software; you can redistribute it and/or modify it 
 * under the terms of the BSD License.
 *
 * This library is distributed in the hope that it will be useful, but 
 * WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 *
 * @package GoogleCheckoutServer
 * @author Byrne Reese <byrne@majordojo.com>
 * @copyright 2006-2008, Byrne Reese.
 */



/**
 * The GoogleCheckoutServer class provides the basic capability of 
 * marshalling a request and response for the server you intend to implement. 
 *
 * The server you implement, depending upon whether you elect to use Google 
 * Checkout's XML or HTML based protocol, will call one of two methods on this
 * class:
 *
 * - handlePost()
 * - handleXML()
 *
 * Example:
 *
 *   require("MyGoogleCheckoutServer.php");
 *   $input = $HTTP_RAW_POST_DATA;
 *   $server = new MyGoogleCheckoutServer();
 *   print $server->handleXML($input);
 *   // or
 *   $server = new MyGoogleCheckoutServer();
 *   print $server->handlePost();
 * 
 * @package GoogleCheckoutServer
 * @abstract 
 */
abstract class Server {
  private $logger;
  function __construct() {
    $this->logger = &Log::factory('file', CHECKOUT_LOG_FILE , 'GOOGLE_CHECKOUT');
  }

  /**
   * This method is invoked for servers that are intended to receive 
   * notifications from Google Checkout via the HTML based protocol.
   * @return GoogleMessage A response message.
   */
  function handlePost() {
    $message = HTMLMessageFactory::create();
    return $this->handle($message);
  }

  /**
   * This method is invoked for servers that are intended to receive 
   * notifications from Google Checkout via the XML based protocol. This 
   * method requires that you  preprocess content from PHP's 
   * $HTTP_RAW_POST_DATA and pass it in yourself.
   *
   * @param string The raw XML request message.
   * @return GoogleMessage A response message.
   */
  function handleXML($request) {
    $message = CheckoutXMLMessageFactory::create($request);
    return $this->handle($message);
  }

  /**
   * This private method takes as input a single GoogleMessage
   * object and routes it through the handler designated for that message
   * type.
   *
   * @param GoogleMessage The request to be processed.
   * @return string The XML response to return to Google.
   * @access private
   */
  function handle($message) {
    if (is_a($message,'NewOrderNotification')) {
      $response = new NotificationAcknowledgment();
      $this->doNewOrderNotification($message,$response);
    } else if (is_a($message,'RiskInformationNotification')) {
      $response = new NotificationAcknowledgment();
      $this->doRiskInformationNotification($message,$response);
      $this->doCharge($message);
    } else if (is_a($message,'OrderStateChangeNotification')) {
      $response = new NotificationAcknowledgment();
      $this->doOrderStateChangeNotification($message,$response);
      $this->doCharge($message);
    } else if (is_a($message,'ChargeAmountNotification')) {
      $response = new NotificationAcknowledgment();
      $this->doChargeAmountNotification($message,$response);
    } else if (is_a($message,'MerchantCalculationCallback')) {
      $response = new MerchantCalculationResults();
      $this->doMerchantCalculationCallback($message,$response);
    } else {
      die("Unknown message type");
    }
    return $response->toXML();
  }

  /**
   * This is the base implementation for processing a charge request. It
   * calls readyToChargeOrder, which queries any subclasses to see if the
   * current order to ready to be charged on the card. This allows 
   * developers to specify their own policy around who and what to charge.
   * This method will then perform the proper action based upon the response
   * from readyToChargeOrder, e.g. send the charge order notification back
   * to Google.
   * 
   * @param GoogleMessage A request message.
   * @access private
   */
  function doCharge($message) {
    $do = $this->readyToChargeOrder($message->orderNumber());
    if ($do == CHARGE) {
      $this->logger->log("Ok to charge order ".$message->orderNumber().", sending notification.");
      $this->sendChargeOrderNotification($message->orderNumber());
    } else if ($do == DONTCHARGE) {
      $this->logger->log("Charge rejected. Not issuing command.");
      // do nothing? sendCancelOrderNotification?
    } else if ($do == WAIT) {
      $this->logger->log("Charge not ready to be processed - waiting...");
      // do nothing
    } else {
      // should never happen
    }
  }
  
  /**
   * This method simply constructs an acknowledgement to tell Google, "yes
   * we got the message."
   *
   * @param GoogleMessage A request message.
   * @return The raw XML of a notification acknowledgement.
   * @access private
   */
  function handleNewOrder($message) {
    $ack = new NotificationAcknowledgment();
    return $ack;
  }

  /**
   * This method sends a charge-order-notification message to Google
   * instructing Google Checkout to initiate the credit card transaction.
   *
   * @param string A Google Checkout order number.
   * @access private
   */
  public function sendChargeOrderNotification($orderNumber) {
    $amount = $this->getOrderAmount($orderNumber);
    $this->logger->log("Issuing charge order command for $amount.");
    $msg = new ChargeOrderNotification($orderNumber,$amount);
    $client = new Client();
    $this->logger->log("Sending: ".$msg->toXML());
    $response = $client->send($msg->toXML());
    $this->logger->log("Response: $response");
  }

  /**
   * This method sends a deliver order notification to Google to change
   * the status of the item to DELIVERED. You may optionally indicate 
   * if you want the purchaser to be notified via email.
   *
   * @param string An Google Checkout order number.
   * @param boolean Whether or not to send an email to the purchaser
   * @access private
   */
  public function sendDeliverOrderNotification($orderNumber,$email) {
    $this->logger->log("Issuing archive order command.");
    $msg = new DeliverOrderNotification($orderNumber,$email);
    $client = new Client();
    $this->logger->log("Sending: ".$msg->toXML());
    $response = $client->send($msg->toXML());
    $this->logger->log("Response: $response");
  }

  /**
   * Sends a message to Google to instruct them to archive the designated
   * order and to remove it from your checkout inbox.
   *
   * @param string An Google Checkout order number.
   * @access private
   */
  public function sendArchiveOrderNotification($orderNumber) {
    $this->logger->log("Issuing archive order command.");
    $msg = new ArchiveOrderNotification($orderNumber);
    $client = new Client();
    $this->logger->log("Sending: ".$msg->toXML());
    $response = $client->send($msg->toXML());
    $this->logger->log("Response: $response");
  }

  /**
   * This abstract method must be implemented by a sub-class. It is
   * responsible for processing in some way a new order placed by a 
   * customer. 
   * 
   * @param NewOrderNotification A request message.
   * @param GoogleMessage A response message.
   * @abstract
   */
  abstract protected function doNewOrderNotification($req,$resp);

  /**
   * This abstract method must be implemented by a sub-class. It is
   * responsible for processing the change in status of an order.
   * 
   * @param OrderStateChangeNotification A request message.
   * @param GoogleMessage A response message.
   * @abstract
   */
  abstract protected function doOrderStateChangeNotification($req,$resp);

  /**
   * This abstract method must be implemented by a sub-class. It is
   * responsible for processing a risk notification from Google.
   * 
   * @param RiskInformationNotification A request message.
   * @param GoogleMessage A response message.
   * @abstract
   */
  abstract protected function doRiskInformationNotification($req,$resp);

  /**
   * This abstract method must be implemented by a sub-class. It is
   * responsible for processing the message indicating that a specific 
   * amount has been charged to the customer.
   * 
   * @param ChargeAmountNotification A request message.
   * @param GoogleMessage A response message.
   * @abstract
   */
  abstract protected function doChargeAmountNotification($req,$resp);

  /**
   * This abstract method must be implemented by a sub-class. It is
   * responsible for calculating any additional merchant charge or
   * order changes, like discount codes, gift certificates, etc.
   * 
   * @param MerchantCalculationCallback A request message.
   * @param GoogleMessage A response message.
   * @abstract
   */
  abstract protected function doMerchantCalculationCallback($req,$resp);

  /**
   * This abstract method must be implemented by a sub-class. It is
   * responsible for processing in some way a new order placed by a 
   * customer. 
   * 
   * @param Order 
   * @return Either: CHARGE, DONTCHARGE, or WAIT
   * @abstract
   */
  abstract protected function readyToChargeOrder($order);

  /**
   * This abstract method must be implemented by a sub-class. It is
   * responsible for calculating and returning the actual order amount, after
   * all discounts and gift certificates have been applied.
   * 
   * @param Order 
   * @return float The amount associated with the related order.
   * @abstract
   */
  abstract protected function getOrderAmount($order);
}
<?php
namespace Utility\Payment\External\GoogleCheckout;

class Order {
	public $orderNumber;
	public $orderTotal;
	public $fulfillmentState;
	public $financialState;
	public $buyerId;
	public $shippingAddress;
	public $billingAddress;
	public $shippingContactName;
	public $createdDate;
}

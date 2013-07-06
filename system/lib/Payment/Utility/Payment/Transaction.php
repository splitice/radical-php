<?php
namespace Utility\Payment;

class Transaction {
	public $id;
	public $order;
	public $date;
	public $sender;
	
	public $gross;
	public $fee;
}
<?php
namespace Utility\Payment;

class Order {
	public $ammount;
	public $name;
	public $item;
	public $address;
	
	function __construct($ammount){
		$this->ammount = $ammount;
	}
}
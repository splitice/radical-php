<?php
namespace Utility\Payment\Modules;
use Utility\Payment\Transaction;
use Utility\Payment\Order;
use Utility\Payment\External;

class Alertpay implements IPaymentModule {
	protected $ipn;
	protected $account;
	protected $p;
	
	function __construct($ipn,$account){
		$this->ipn = $ipn;
		$this->account = $account;
		
		$this->p = new External\Alertpay();
		
		if($this->sandbox)
			$this->p->url = self::SANDBOX_URL;
		
		$this->p->add_field ( 'ap_merchant', $this->account );
		$this->p->add_field ( 'ap_returnurl', $this->ipn . '?action=success' );
		$this->p->add_field ( 'ap_cancelurl', $this->ipn . '?action=cancel' );
		$this->p->add_field ( 'notify_url', $this->ipn . '?action=ipn' );
	}
	private $sandbox;
	function sandboxed($is){
		$this->sandbox = $is;
		if($is){
			$this->p->url = External\Alertpay::URL_SANDBOX;
			$this->p->ipn = External\Alertpay::IPN_SANDBOX;
		}else{
			$this->p->url = External\Alertpay::URL;
			$this->p->ipn = External\Alertpay::IPN;
		}
	}
	function bill($order){
		if(!is_object($order))
			$order = new Order($order);
		
		if($order->name)
			$this->p->add_field ( 'ap_itemname', $order->name );
		
		$this->p->add_field ( 'ap_amount', $order->ammount );
		
		if($order->item)
			$this->p->add_field ('ap_itemcode', $order->item );
		
		$this->p->submit ();
	}
	function subscribe($ammount){
		
	}
	function ipn(){	
		if ($this->p->validate_ipn () && $this->p->ipn_data['ap_status'] == 'Success') {
			$transaction = new Transaction();
			$transaction->id = $this->p->ipn_data['ap_referencenumber'];
			
			$transaction->gross = $this->p->ipn_data ['ap_totalamount'];
			$transaction->fee = $this->p->ipn_data['ap_feeamount'];
			
			$order = new Order($transaction->gross - $transaction->fee);
			$order->name = $this->p->ipn_data['ap_itemname'];
			$order->item = $this->p->ipn_data['ap_itemcode'];
			
			$transaction->order = $order;
			
			return $transaction;
		}
	}
}
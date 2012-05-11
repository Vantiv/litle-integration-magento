<?php
class Litle_CreditCard_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isStateOfOrderEqualTo($order, $inOrderState){
		$payment = $order->getPayment();
		$lastTxnId = $payment->getLastTransId();
		$lastTxn = $payment->getTransaction($lastTxnId);
		
		if( $lastTxn->getTxnType() === $inOrderState )
			return true;
		else
			return false;
	}
	
	// TODO:: Needs to be implemented.
	public function isMOPLitleCC($mop){
		return ($mop === "creditcard");
	}
	
	// TODO:: Needs to be implemented.
	public function isMOPLitleECheck($mop){
		return ($mop === "lecheck");
	}
	
	public function isMOPLitle($payment){
		$mop = $payment->getData('method');
		return ($this->isMOPLitleCC($mop) || $this->isMOPLitleECheck($mop));
	}
}

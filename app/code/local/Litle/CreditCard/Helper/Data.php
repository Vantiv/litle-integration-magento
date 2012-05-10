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
	public function isMOPLitleCC(){
		return true;
	}
	
	// TODO:: Needs to be implemented.
	public function isMOPLitleECheck(){
		return true;
	}
	
	public function isMOPLitle(){
		return ($this->isMOPLitleCC() || $this->isMOPLitleECheck());
	}
}

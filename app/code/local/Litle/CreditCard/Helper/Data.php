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

	public function uniqueCreditCard($customerId) {
		$collection = array();
		$collection = Mage::getModel('palorus/vault')
		->getCollection()
		->addFieldToFilter('customer_id',$customerId);
		
		$purchases = array();
		$unique = array();
		$i=0;
		foreach ($collection as $purchase) {
			$purchases[$i] = $purchase->getData();
			$i++;
		}
		
		return $this->populateStoredCreditCard($purchases);
	}
	
	public function populateStoredCreditCard($purchases) {
		
		$unique = array();
		$unique[0] = $purchases[0];
		for ($y=1; $y < count($purchases); $y++){
			$setter = 0;
			for ($x=0; $x <= count($unique); $x++){
				if (($purchases[$y]['type'] === $unique[$x]['type']) && ($purchases[$y]['last4'] === $unique[$x]['last4']))
				{
					$setter = 1;
				}
			}
			if ($setter === 0)
			{
				array_push($unique, $purchases[$y]);
			}
		}
		return $unique;
	}
}

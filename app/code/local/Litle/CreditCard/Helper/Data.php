<?php
class Litle_CreditCard_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function isStateOfOrderEqualTo($order, $inOrderState){
		$payment = $order->getPayment();
		$lastTxnId = $payment->getLastTransId();
		Mage::log("Last txn id: " . $lastTxnId);
		$lastTxn = $payment->getTransaction($lastTxnId);

		if( $lastTxn != null && $lastTxn->getTxnType() === $inOrderState )
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

	// This method converts dollars to cents, and takes care of trailing decimals if any.
	public function formatAmount($amountInDecimal, $roundUp) {
		if( empty($amountInDecimal) || $amountInDecimal === "" )
			return $amountInDecimal;

		$amountInCents = ((double)$amountInDecimal) * 100;
		$amountToReturn = (int)$amountInCents;

		// check to see if we have left over decimals -- i.e. the incoming amount had more than 2 decimals
		if( $amountInCents != (double)$amountToReturn)
		{
			// yes, more decimals than needed indeed!
			$amountToReturn = ($roundUp) ? ($amountToReturn + 1) : ($amountToReturn);
		}
		return $amountToReturn;
	}

	/**
	 * Are we using the sandbox?
	 *
	 * @return boolean
	 */
	public function isSandbox()
	{
		$url = Mage::getStoreConfig('payment/CreditCard/url');
		return (stristr($url, '.testlitle.com/sandbox') !== false);
	}
}

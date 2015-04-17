<?php

class Litle_LPaypal_Helper_Data extends Mage_Payment_Helper_Data
{
	/**
     * Get and sort available payment methods for specified or current store 
     * Plus filter out the Litle Paypal method
     *
     * array structure:
     *  $index => Varien_Simplexml_Element
     *
     * @param mixed $store
     * @param Mage_Sales_Model_Quote $quote
     * @return array
     */
    public function getStoreMethods($store = null, $quote = null)
    {
    	$methods = parent::getStoreMethods($store, $quote);
    	$LPaypalIndex = null;
    	for ($i = 0; $i < sizeof($methods); $i++){
    		if ('lpaypal' == $methods[$i]->getCode()){
    			$LPaypalIndex = $i;
    		}
    	}
    	if (null !== $LPaypalIndex){
    		unset($methods[$LPaypalIndex]);
    	}
    	return $methods;
    }



    public function isLastTxnCreatedLessThanDays(Varien_Object $payment, $days)
    {
        $lastTxnId = $payment->getLastTransId();
        $lastTxn = $payment->getTransaction($lastTxnId);
        $timeOfLastTxn = $lastTxn->getData('created_at');

        $seconds = $days * 24 * 3600;
        return ((time()-strtotime($timeOfLastTxn)) < $seconds);
    }

    public function isLastAuthCreatedBeforeDays(Varien_Object $authTxn, $days)
    {
        $authTransactionTime = strtotime($authTxn->getCreatedAt());
        $seconds = $days * 24 * 3600;
        return ($authTransactionTime < time() - $seconds);
    }
}
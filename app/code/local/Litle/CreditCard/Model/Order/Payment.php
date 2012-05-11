<?php

class Litle_CreditCard_Model_Order_Payment extends Mage_Sales_Model_Order_Payment
{
//     /**
//      * Cancel a creditmemo: substract its totals from the payment
//      *
//      * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
//      * @return Mage_Sales_Model_Order_Payment
//      */
//     public function cancelCreditmemo($creditmemo)
//     {
//         $this->_updateTotals(array(
//             'amount_refunded' => -1 * $creditmemo->getGrandTotal(),
//             'base_amount_refunded' => -1 * $creditmemo->getBaseGrandTotal(),
//             'shipping_refunded' => -1 * $creditmemo->getShippingAmount(),
//             'base_shipping_refunded' => -1 * $creditmemo->getBaseShippingAmount()
//         ));
//         Mage::dispatchEvent('sales_order_payment_cancel_creditmemo',
//             array('payment' => $this, 'creditmemo' => $creditmemo)
//         );
//         return $this;
//     }

    protected function _reverseRefund($isOnline, $amount = null, $gatewayCallback = 'void')
    {
    	$order = $this->getOrder();
    	// attempt to void
    	if ($isOnline) {
    		$this->getMethodInstance()->setStore($order->getStoreId())->$gatewayCallback($this);
    	}
    	if ($this->_isTransactionExists()) {
    		return $this;
    	}
    	
    	foreach($order->getItemsCollection() as $item){
    		if ($item->getQtyRefunded() > 0) 
    			$item->setQtyRefunded(0)->save();
    	}
    	
    	$order
    	->setBaseDiscountRefunded(0)
    	->setBaseShippingRefunded(0)
    	->setBaseSubtotalRefunded(0)
    	->setBaseTaxRefunded(0)
    	->setBaseShippingTaxRefunded(0)
    	->setBaseTotalOnlineRefunded(0)
    	->setBaseTotalOfflineRefunded(0)
    	->setBaseTotalRefunded(0)
    	->setTotalOnlineRefunded(0)
    	->setTotalOfflineRefunded(0)
    	->setDiscountRefunded(0)
    	->setShippingRefunded(0)
    	->setShippingTaxRefunded(0)
    	->setSubtotalRefunded(0)
    	->setTaxRefunded(0)
    	->setTotalRefunded(0);
    	
    	// update transactions, order state and add comments
    	$transaction = $this->_addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID, null, true);
    	$message = $this->hasMessage() ? $this->getMessage() : "Voided Refund.";
    	$message = $this->_prependMessage($message);
    	$message = $this->_appendTransactionToMessage($transaction, $message);
    	$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $message);
    }
    
    /**
     * Void payment either online or offline (process void notification)
     * NOTE: that in some cases authorization can be voided after a capture. In such case it makes sense to use
     *       the amount void amount, for informational purposes.
     * Updates payment totals, updates order status and adds proper comments
     *
     * @param bool $isOnline
     * @param float $amount
     * @param string $gatewayCallback
     * @return Mage_Sales_Model_Order_Payment
     */
    protected function _void($isOnline, $amount = null, $gatewayCallback = 'void')
    {
        if(Mage::helper("creditcard")->isMOPLitle($this))
        {
        	$order = $this->getOrder();
        	if(Mage::helper("creditcard")->isStateOfOrderEqualTo($order, Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND))
        	{	
        		$this->_reverseRefund($isOnline, $amount, $gatewayCallback);
        	} else if(Mage::helper("creditcard")->isStateOfOrderEqualTo($order, Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH)){
        		parent::_void($isOnline, $amount, $gatewayCallback);
        	} else if(Mage::helper("creditcard")->isStateOfOrderEqualTo($order, Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE)){
        		Mage::throwException("Order needs to be refunded prior to cancellation.");
        	} else {
        		parent::_void($isOnline, $amount, $gatewayCallback);
        	}
        } else {
        	parent::_void($isOnline, $amount, $gatewayCallback);
        }
        
        return $this;
    }
}

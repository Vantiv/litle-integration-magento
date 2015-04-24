<?php
/**
 * Magento Plieninger Editable Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 *
 * @category   Plieninger
 * @package    Plieninger_Editable
 * @copyright  Copyright (c) 2009 Andreas Plieninger
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL)
 * @author     Andreas Plieninger <aplieninger@gmx.de> www.plieninger.org
 * @version    0.1.0
 * @date       18.12.2009
*/


class Litle_CreditCard_Block_Adminhtml_Orderview extends Mage_Adminhtml_Block_Sales_Order_View {
		
	public function __construct() {
		parent::__construct();

        $order = $this->getOrder();
	    if(Mage::helper("creditcard")->isMOPLitle($order->getPayment()))
		{
            $authTransaction = $order->getPayment()->lookupTransaction(false, Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
            // check if Auth-Reversal need to be shown
            if (!(Mage::helper("creditcard")->isMOPLitleECheck($order->getPayment()->getData('method')))){
                if($authTransaction && !$authTransaction->getIsClosed())
                {
                    if ($order->getPayment()->getAmountPaid() == 0){
                        $message = 'Are you sure you want to reverse the authorization?';
                        $this->_updateButton('void_payment', 'label','Auth-Reversal');
                        $this->_updateButton('void_payment', 'onclick', "confirmSetLocation('{$message}', '{$this->getVoidPaymentUrl()}')");
                    }
                }
                else{
                    $this->removeButton('order_invoice');
                }
            }

// 			check if Void-Refund needs to be shown		
			if( Mage::helper("creditcard")->isStateOfOrderEqualTo($order, Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND))
			{
				$onclickJs = 'deleteConfirm(\''
				. Mage::helper('sales')->__('Are you sure? The refund request will be canceled.')
				. '\', \'' . $this->getVoidPaymentUrl() . '\');';
				
				$this->_addButton('void_refund', array(
				                'label'    => 'Void Refund',
				                'onclick'  => $onclickJs,
				));
			}
			//check if void capture or void sale needs to be shown
			if(Mage::helper("creditcard")->isStateOfOrderEqualTo($order, Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE) &&
				$this->wasLastTxnLessThan24HrsAgo($order->getPayment()))
			{
				$mop = $order->getPayment()->getData('method');
				//check if paying with a credit card
				if(Mage::helper("creditcard")->isMOPLitleCC($mop) || Mage::helper("creditcard")->isMOPLitlePaypal($mop)){
                    $onclickJs = 'deleteConfirm(\''
					. Mage::helper('sales')->__('Are you sure?  If any previous partial captures were done on this order, or if capture was not done today then do a refund instead.')
					. '\', \'' . $this->getVoidPaymentUrl() . '\');';
				
					$this->_addButton('void_capture', array(
								                'label'    => 'Void Capture',
								                'onclick'  => $onclickJs,
					));
				}
				//check if paying with Litle echeck
				elseif(Mage::helper("creditcard")->isMOPLitleECheck($mop)){
					$onclickJs = 'deleteConfirm(\''
					. Mage::helper('sales')->__('Are you sure?  If any previous partial captures were done on this order, or if capture was not done today then do a refund instead.')
					. '\', \'' . $this->getVoidPaymentUrl() . '\');';
					
					$this->_addButton('void_sale', array(
                                                'label'    => 'Void Sale',
                                                'onclick'  => $onclickJs,
					));
				}
			}

		}
	}
	
	public function wasLastTxnLessThan24HrsAgo(Varien_Object $payment)
	{
		$lastTxnId = $payment->getLastTransId();
		$lastTxn = $payment->getTransaction($lastTxnId);
		$timeOfLastTxn = $lastTxn->getData('created_at');
	
		//check if last txn was less than 24 hrs ago (86400 seconds == 24 hrs)
		return ((time()-strtotime($timeOfLastTxn)) < 86400);
	}
  
}
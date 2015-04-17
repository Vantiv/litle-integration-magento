<?php
/**
 * Created by PhpStorm.
 * User: joncai
 * Date: 3/27/15
 * Time: 5:48 PM
 */ 
class Litle_LPaypal_Block_Adminhtml_Sales_Order_View extends Mage_Adminhtml_Block_Sales_Order_View {

    public function __construct() {
        parent::__construct();

        Mage::log('I am here with lpaypal', null, 'lpaypal_be.log');


        $order = $this->getOrder();
        if(Mage::helper("lpaypal")->isLitlePaypal($order->getPayment()))
        {
// 			check if Auth-Reversal needs to be shown
            if( Mage::helper("creditcard")->isStateOfOrderEqualTo($order, Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH))
            {
                $message = 'Are you sure you want to reverse the authorization?';
                $this->_updateButton('void_payment', 'label','Auth-Reversal');
                $this->_updateButton('void_payment', 'onclick', "confirmSetLocation('{$message}', '{$this->getVoidPaymentUrl()}')");
            }
// 			check if Void-Refund needs to be shown
            else if( Mage::helper("creditcard")->isStateOfOrderEqualTo($order, Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND))
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
            else if(Mage::helper("creditcard")->isStateOfOrderEqualTo($order, Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE) &&
                Mage::helper('lpaypal')->isLastTxnCreatedLessThanDays($order->getPayment(), 2))
            {
                $onclickJs = 'deleteConfirm(\''
                    . Mage::helper('sales')->__('Are you sure?  If any previous partial captures were done on this order, or if capture was not done today then do a refund instead.')
                    . '\', \'' . $this->getVoidPaymentUrl() . '\');';

                $this->_addButton('void_capture', array(
                    'label'    => 'Void Capture',
                    'onclick'  => $onclickJs,
                ));
//                //check if paying with a credit card
//                if(Mage::helper("creditcard")->isMOPLitleCC($mop)){
//                    $onclickJs = 'deleteConfirm(\''
//                        . Mage::helper('sales')->__('Are you sure?  If any previous partial captures were done on this order, or if capture was not done today then do a refund instead.')
//                        . '\', \'' . $this->getVoidPaymentUrl() . '\');';
//
//                    $this->_addButton('void_capture', array(
//                        'label'    => 'Void Capture',
//                        'onclick'  => $onclickJs,
//                    ));
//                }
//                //check if paying with Litle echeck
//                elseif(Mage::helper("creditcard")->isMOPLitleECheck($mop)){
//                    $onclickJs = 'deleteConfirm(\''
//                        . Mage::helper('sales')->__('Are you sure?  If any previous partial captures were done on this order, or if capture was not done today then do a refund instead.')
//                        . '\', \'' . $this->getVoidPaymentUrl() . '\');';
//
//                    $this->_addButton('void_sale', array(
//                        'label'    => 'Void Sale',
//                        'onclick'  => $onclickJs,
//                    ));
//                }
            }
        }
    }
}
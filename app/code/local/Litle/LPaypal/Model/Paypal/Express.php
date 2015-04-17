<?php
/**
 * Created by PhpStorm.
 * User: joncai
 * Date: 3/26/15
 * Time: 11:16 AM
 */
require_once('Litle/LitleSDK/LitleOnline.php');

class Litle_LPaypal_Model_Paypal_Express extends Mage_Paypal_Model_Express {

    public function order(Varien_Object $payment, $amount)
    {
        if (Mage::getStoreConfig('payment/LPaypal/active') &&
            Mage::getStoreConfig('payment/paypal_express/payment_action') == 'Order' &&
            Mage::getStoreConfig('payment/paypal_express/active')){
            $this->_order($payment, $amount);
        }else{
            parent::order($payment, $amount);
        }
    }

    public function _order($payment, $amount)
    {
        // do Paypal order transaction
        parent::_placeOrder($payment, $amount);

        // process paypal order transaction result
        $payment->setAdditionalInformation($this->_isOrderPaymentActionKey, true);

        if ($payment->getIsFraudDetected()) {
            return $this;
        }

        // do Litle transaction
        $order = $payment->getOrder();
        $payment -> setMethod('lpaypal');
        $methodInstance = Mage::helper('payment')->getMethodInstance($payment->getMethod());
        $methodInstance->setInfoInstance($payment);
        $payment->setMethodInstance($methodInstance);
        $transactionType = Mage::getStoreConfig('payment/LPaypal/payment_action');
        switch($transactionType){
            case Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE:
                $payment->authorizeForLPaypal(true, $order->getBaseTotalDue()); // base amount will be set inside
                $payment->setAmountAuthorized($order->getTotalDue());
                break;
            case Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE:
                $payment->setAmountAuthorized($order->getTotalDue());
                $payment->setBaseAmountAuthorized($order->getBaseTotalDue());
                $payment->capture(null);
                break;
            default:
                break;
        }

        $payment->setSkipOrderProcessing(true);
        return $this;
    }
}

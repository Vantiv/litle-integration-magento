<?php
/**
 * Created by PhpStorm.
 * User: jcai
 * Date: 4/6/15
 * Time: 1:51 PM
 */
require_once 'Mage/Adminhtml/controllers/System/ConfigController.php';


class Litle_LPaypal_Adminhtml_System_ConfigController extends Mage_Adminhtml_System_ConfigController {
    public function saveAction(){
        parent::saveAction();
        if (Mage::getStoreConfig('payment/paypal_express/payment_action') != 'Order' ||
            !Mage::getStoreConfig('payment/paypal_express/active')){
            $config = new Mage_Core_Model_Config();
            $config ->saveConfig('payment/LPaypal/active', "0", 'default', 0);
        }
    }
}
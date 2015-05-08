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
    	$LPaypalWasOn = (Mage::getStoreConfig('payment/LPaypal/active')) ? True : False;

        parent::saveAction();

		$config = new Mage_Core_Model_Config();
        if (!Mage::getStoreConfig('payment/LPaypal/active')){
    		// if merchant turn off litle paypal, turn off the paypal express checkout either
    		if ($LPaypalWasOn){
        		$config ->saveConfig('payment/paypal_express/active', "0", 'default', 0);	
    		}
    	} else {
    		// if merchant turn on litle paypal, turn on paypal express chekout automatically and set the payment action to Order
        	$config ->saveConfig('payment/paypal_express/active', "1", 'default', 0);
        	$config ->saveConfig('payment/paypal_express/payment_action', "Order", 'default', 0);
    	}
    }
}
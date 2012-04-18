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

class Litle_Editable_Adminhtml_EditableController extends Mage_Adminhtml_Controller_action
{
	private $_order;

	public function saveAction() {
		$field = $this->getRequest()->getParam('field');
		$type = $this->getRequest()->getParam('type');
		$orderId = $this->getRequest()->getParam('order');
		$value = $this->getRequest()->getPost('value');
		if (!empty($field) && !empty($type) && !empty($orderId)) {
			if(!empty($value)) {
				if(!$this->_loadOrder($orderId)) {
					$this->getResponse()->setBody($this->__('error: missing order'));
				}
				$res = $this->_editAddress($type,$field,$value);
				if($res !== true) {
					$this->getResponse()->setBody($this->__('error: '.$res));
				} else {
					$this->getResponse()->setBody($value);
				}
			} else {
				$this->getResponse()->setBody($this->__('error: value required'));
			}
		} else {
			$this->getResponse()->setBody('error');
		}
	}

	private function _loadOrder($orderId) {
		$this->_order = Mage::getModel('sales/order')->load($orderId);
		if(!$this->_order->getId()) return false;
		return true;
	}

	private function _editAddress($type,$field,$value) {
		if($type == 1) {
			$address = $this->_order->getBillingAddress();
			$addressSet = 'setBillingAddress';
		} elseif($type == 2) {
			$address = $this->_order->getShippingAddress();
			$addressSet = 'setShippingAddress';
		} else {
			return 'type not found';
		}

		$updated = false;
    	$fieldGet = 'get'.ucwords($field);
    	$fieldSet = 'set'.ucwords($field);

//update von country noch ein problem!
    	if($address->$fieldGet() != $value) {
    		if($field == 'country') {
    			$fieldSet = 'setCountryId';
    			$countries = array_flip(Mage::app()->getLocale()->getCountryTranslationList());
    			if(isset($countries[$value])) {
    				$value = $countries[$value];
    			} else {
    				return 'country not found';
    			}
    		}
    		if(substr($field,0,6) == 'street') {
    			$i = substr($field,6,1);
    			if(!is_numeric($i))
    				$i = 1;
    			$valueOrg = $value;
    			$value = array();
    			for($n=1;$n<=4;$n++) {
    				if($n != $i) {
	    				$value[] = $address->getStreet($n);
    				} else {
    					$value[] = $valueOrg;
    				}
    			}
    			$fieldSet = 'setStreet';
    		}
    		//update field and set as updated
    		$address->$fieldSet($value);
    		$updated = true;
    	}

		if($updated) {
			$this->_order->$addressSet($address);
        	$this->_order->save();
		}
		return true;
	}
}
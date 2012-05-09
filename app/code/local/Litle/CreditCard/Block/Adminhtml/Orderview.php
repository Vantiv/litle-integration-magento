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
		//$this->removeButton('void_payment');
		
	    if(Mage::helper("creditcard")->isMOPLitle())
		{
			$this->order = $this->getOrder();
			$this->payment = $this->order->getPayment();
			$this->lastTxnId = $this->payment->getLastTransId();
			$this->lastTxn = $this->payment->getTransaction($this->lastTxnId);
			// check if Auth-Reversal needs to be shown
			if( $this->canDo(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH) )
			{
				$message = 'Are you sure you want to reverse the authorization?';
				$this->_updateButton('void_payment', 'label','Auth-Reversal');
				$this->_updateButton('void_payment', 'onclick', "confirmSetLocation('{$message}', '{$this->getVoidPaymentUrl()}')");
			}
			// check if Void-Refund needs to be shown
			else if( $this->canDo(Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND) )
			{
				$onclickJs = 'deleteConfirm(\''
				. Mage::helper('sales')->__('Are you sure? The refund request will be canceled.')
				. '\', \'' . $this->getVoidPaymentUrl() . '\');';
				
				$this->_addButton('void_refund', array(
				                'label'    => 'Void Refund',
				                'onclick'  => $onclickJs,
				));
			}
		}
	}

	protected function _beforeToHtml() {
		parent::_beforeToHtml();
	}
	
	public function canDo($typeToDo){
		if( $this->lastTxn->getTxnType() === $typeToDo )
			return true;
		else
			return false;
	}
		
}
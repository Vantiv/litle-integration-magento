<?php
class Litle_CreditCard_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action{

	public function massCaptureAction()
		{
			Mage::log('in mass capture');
			$orderIds = $this->getRequest()->getPost('order_ids', array());
			$countCancelOrder = 0;
			$countNonCancelOrder = 0;
			foreach ($orderIds as $orderId) {
				$order = Mage::getModel('sales/order')->load($orderId);
				if ($order->canCancel()) {
					$order->cancel()
					->save();
					$countCancelOrder++;
				} else {
					$countNonCancelOrder++;
				}
			}
			if ($countNonCancelOrder) {
				if ($countCancelOrder) {
					$this->_getSession()->addError($this->__('%s order(s) cannot be canceled', $countNonCancelOrder));
				} else {
					$this->_getSession()->addError($this->__('The order(s) cannot be canceled'));
				}
			}
			if ($countCancelOrder) {
				$this->_getSession()->addSuccess($this->__('%s order(s) have been canceled.', $countCancelOrder));
			}
			$this->_redirectUrl(Mage::helper('palorus')->getBaseUrl() . '/sales_order');
		}

}
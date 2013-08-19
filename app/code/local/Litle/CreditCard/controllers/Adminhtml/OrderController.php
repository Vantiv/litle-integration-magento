<?php
class Litle_CreditCard_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action{

	public function massCaptureAction()
	{
		$orderIds = $this->getRequest()->getPost('order_ids', array());
		foreach ($orderIds as $orderId) {
			$order = Mage::getModel('sales/order')->load($orderId);
			$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice(array());
			if ($order->canInvoice()){
				$invoice->register();
				if($this->captureInvoice($invoice)) {
					$this->_getSession()->addSuccess("The order #".$invoice->getOrder()->getIncrementId()." captured successfully");
				}
			} else {
				$this->_getSession()->addError($this->__('The order #' .  $invoice->getOrder()->getIncrementId() . ' cannot be Captured '));
			}
		}
		$referrer = $_SERVER['HTTP_REFERER'];
		$this->_redirectUrl($referrer);
	}

	private function captureInvoice($invoice)
	{
		try
		{
			$invoice->setRequestedCaptureCase('online');
			//$invoice->sendEmail(true);
			//$invoice->setEmailSent(true);
			//$invoice->getOrder()->setCustomerNoteNotify(true);
			//$invoice->getOrder()->setIsInProcess(true);
			$invoice->capture();
			$transactionSave = Mage::getModel('core/resource_transaction')
			->addObject($invoice)
			->addObject($invoice->getOrder());
			$transactionSave->save();
			return true;
		}
		catch (Exception $e)
		{
			Mage::getSingleton('core/session')->addError($e->getMessage());
			Mage::logException($e);
			return false;
		}
	}
}
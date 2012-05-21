<?php
class Litle_CreditCard_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action{

	public function massCaptureAction()
	{
		$orderIds = $this->getRequest()->getPost('order_ids', array());
		$countCaptureOrder = 0;
		foreach ($orderIds as $orderId) {
			$order = Mage::getModel('sales/order')->load($orderId);
			$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice(array());
			if ($order->canInvoice()){
				$invoice->register();
				$this->captureInvoice($invoice);
				$countCaptureOrder++;
			} else {
				$this->_getSession()->addError($this->__('The order #' .  $invoice->getOrder()->getIncrementId() . ' cannot be Captured '));
			}
		}
		if ($countCaptureOrder) {
			$this->_getSession()->addSuccess($this->__('%s order(s) have been Captured', $countCaptureOrder));
		}
		$referrer = $_SERVER['HTTP_REFERER'];
		$this->_redirectUrl($referrer);
	}

	private function captureInvoice($invoice)
	{
		try
		{
			$invoice->setRequestedCaptureCase('online');
			$invoice->sendEmail(true);
			$invoice->setEmailSent(true);
			$invoice->getOrder()->setCustomerNoteNotify(true);
			$invoice->getOrder()->setIsInProcess(true);
			$invoice->capture();
			$transactionSave = Mage::getModel('core/resource_transaction')
			->addObject($invoice)
			->addObject($invoice->getOrder());
			$transactionSave->save();
		}

		catch (Exception $e)
		{
			Mage::logException($e);
		}
	}
}
<?php
class Litle_CreditCard_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action{

	public function massCaptureAction()
	{
		$orderIds = $this->getRequest()->getPost('order_ids', array());
		$countCancelOrder = 0;
		$countNonCancelOrder = 0;
		foreach ($orderIds as $orderId) {
			$order = Mage::getModel('sales/order')->load($orderId);
			if ($order->canInvoice()){
				$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice(array());
				$invoice->register();

				$this->captureInvoice($invoice);
				$countCancelOrder++;
				//}

			} else {
				$countNonCancelOrder++;
			}
		}
		if ($countNonCancelOrder) {
			if ($countCancelOrder) {
				$this->_getSession()->addError($this->__('%s order(s) cannot be Captured', $countNonCancelOrder));
			} else {
				$this->_getSession()->addError($this->__('The order(s) cannot be Captured'));
			}
		}
		if ($countCancelOrder) {
			$this->_getSession()->addSuccess($this->__('%s order(s) have been Captured', $countCancelOrder));
		}
		$referrer = $_SERVER['HTTP_REFERER'];
		$this->_redirectUrl($referrer);
	}
	
	private function captureInvoice($invoice)
	{
		// If no products add an error.
		if (!$invoice->getTotalQty())
		{
			$this->_getSession()->addError($this->__('Order # '.$invoice->getOrder()->getIncrementId().': Cannot create an invoice without products.'));
		}
		else
		{
			// Set capture case to online and register the invoice.
			$invoice->setRequestedCaptureCase('online');
				
			// Try and send the customer notification email.
			try
			{
				$invoice->sendEmail(true);
				$invoice->setEmailSent(true);
				$invoice->getOrder()->setCustomerNoteNotify(true);
			}
			// Catch exceptions.
			catch (Exception $e)
			{
				Mage::logException($e);
			}
			
			// Capture invoice.
			$invoice->getOrder()->setIsInProcess(true);
			$invoice->capture();

			// Go grab order from external resource and capture (etc. paypal, worldpay).
			$transactionSave = Mage::getModel('core/resource_transaction')
			->addObject($invoice)
			->addObject($invoice->getOrder());
			$transactionSave->save();

		}
	}
}
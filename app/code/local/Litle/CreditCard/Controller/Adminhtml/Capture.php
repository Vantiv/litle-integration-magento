<?php
class Litle_Adminhtml_CreditCard_Capture extends Mage_Adminhtml_Sales_OrderController{

	public function massCaptureAction()
	{
 		$orderIds = $this->getRequest()->getPost('order_ids', array());
		Mage::throwException('herhe');

// 		// Loop through orders individually.
// 		for ($i=0; $iload($orderIds[$i]);

// 		try
// 		{
// 			// Check if order can be invoiced
// 			if (!$order->canInvoice())
// 			{
// 				// If cannot invoice order, check if there are any pending invoices.
// 				if ($order->hasInvoices())
// 				{
// 					// Loop through the invoices.
// 					foreach($order->getInvoiceCollection() as $invoice)
// 					{
// 						// If invoice state is equal to open (Pending) then capture the invoice else throw an error.
// 						if ($invoice->getState() == Mage_Sales_Model_Order_Invoice::STATE_OPEN)
// 						{
// 							$this->captureInvoice($invoice);
// 						}
// 						else
// 						{
// 							$this->_getSession()->ddError($this->__('Order # '.$order->getIncrementId().': The order does not allow creating an invoice'));
// 						}
// 					}
// 				}
// 				else
// 				{
// 					// Capture invoice
// 					$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice(array());
// 					$invoice->register();
// 					$this->captureInvoice($invoice);
// 				}
// 			}

// 		}
// // 		// Catch Magento errors.
// // 		catch (Mage_Core_Exception $e)
// // 		{
// // 			$this->_getSession()->addError($e->getMessage());
// // 		}
// // 		// Catch other errors.
// // 		catch (Exception $e)
// // 		{
// // 			$this->_getSession()->addError($this->__('Order # '.$order->getIncrementId().': Unable to save the invoice.'));
// // 			Mage::logException($e);
// // 		}

// // 		// Redirect back to sales -> order page.
//  		$this->_redirect('*/*/');
// 	}

// 	/**
// 	 * Capture any invoice.
// 	 */
// // 	private function captureInvoice($invoice)
// // 	{
// // 		// If no products add an error.
// // 		if (!$invoice->getTotalQty())
// // 		{
// // 			$this->_getSession()->addError($this->__('Order # '.$invoice->getOrder()->getIncrementId().': Cannot create an invoice without products.'));
				
// // 		}
// // 		else
// // 		{
// // 			// Set capture case to online and register the invoice.
// // 			$invoice->setRequestedCaptureCase('online');
				
// // 			// Try and send the customer notification email.
// // 			try
// // 			{
// // 				$invoice->sendEmail(true);
// // 				$invoice->setEmailSent(true);
// // 				$invoice->getOrder()->setCustomerNoteNotify(true);
// // 			}
// // 			// Catch exceptions.
// // 			catch (Exception $e)
// // 			{
// // 				Mage::logException($e);
// // 				$this->_getSession()->addError('Order # '.$invoice->getOrder()->getIncrementId().': '. $this->__('Unable to send the invoice email.'));
// // 			}
// // 			// Capture invoice.
// // 			$invoice->getOrder()->setIsInProcess(true);
// // 			$invoice->capture();

// // 			// Go grab order from external resource and capture (etc. paypal, worldpay).
// // 			$transactionSave = Mage::getModel('core/resource_transaction')
// // 			->addObject($invoice)
// // 			->addObject($invoice->getOrder());
// // 			$transactionSave->save();

// // 			// Success message.
// // 			$this->_getSession()->addSuccess($this->__('Order # '.$invoice->getOrder()->getIncrementId().': The invoice for order has been captured.'));
// // 		}
// // 	}

}
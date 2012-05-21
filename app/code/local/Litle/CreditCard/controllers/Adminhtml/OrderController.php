<?php
class Litle_CreditCard_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action{

	public function massCaptureAction()
	{
		$orderIds = $this->getRequest()->getPost('order_ids', array());
		$countCancelOrder = 0;
		$countNonCancelOrder = 0;
		foreach ($orderIds as $orderId) {
			$order = Mage::getModel('sales/order')->load($orderId);
			if (true){


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
				$this->_getSession()->addError('Order # '.$invoice->getOrder()->getIncrementId().': '. $this->__('Unable to send the invoice email.'));
			}
			// Capture invoice.
			$invoice->getOrder()->setIsInProcess(true);
			$invoice->capture();

			// Go grab order from external resource and capture (etc. paypal, worldpay).
			$transactionSave = Mage::getModel('core/resource_transaction')
			->addObject($invoice)
			->addObject($invoice->getOrder());
			$transactionSave->save();

			// Success message.
			$this->_getSession()->addSuccess($this->__('Order # '.$invoice->getOrder()->getIncrementId().': The invoice for order has been captured.'));
		}
	}
	// 	public function captureAction()
	// 	{
	// 		if ($invoice = $this->_initInvoice()) {
	// 			try {
	// 				$invoice->capture();
	// 				$this->_saveInvoice($invoice);
	// 				$this->_getSession()->addSuccess($this->__('The invoice has been captured.'));
	// 			} catch (Mage_Core_Exception $e) {
	// 				$this->_getSession()->addError($e->getMessage());
	// 			} catch (Exception $e) {
	// 				$this->_getSession()->addError($this->__('Invoice capturing error.'));
	// 			}
	// 			$this->_redirect('*/*/view', array('invoice_id'=>$invoice->getId()));
	// 		} else {
	// 			$this->_forward('noRoute');
	// 		}
	// 	}

	protected function _initInvoice($update = false)
	{
		$this->_title($this->__('Sales'))->_title($this->__('Invoices'));

		$invoice = false;
		$itemsToInvoice = 0;
		$invoiceId = $this->getRequest()->getParam('invoice_id');
		$orderId = $this->getRequest()->getParam('order_id');
		if ($invoiceId) {
			$invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
			if (!$invoice->getId()) {
				$this->_getSession()->addError($this->__('The invoice no longer exists.'));
				return false;
			}
		} elseif ($orderId) {
			$order = Mage::getModel('sales/order')->load($orderId);
			/**
			 * Check order existing
			 */
			if (!$order->getId()) {
				$this->_getSession()->addError($this->__('The order no longer exists.'));
				return false;
			}
			/**
			 * Check invoice create availability
			 */
			if (!$order->canInvoice()) {
				$this->_getSession()->addError($this->__('The order does not allow creating an invoice.'));
				return false;
			}
			$savedQtys = $this->_getItemQtys();
			$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
			if (!$invoice->getTotalQty()) {
				Mage::throwException($this->__('Cannot create an invoice without products.'));
			}
		}
	}

	public function save()
	{
		$data = $this->getRequest()->getPost('invoice');
		$orderId = $this->getRequest()->getParam('order_id');
			
		if (!empty($data['comment_text'])) {
			Mage::getSingleton('adminhtml/session')->setCommentText($data['comment_text']);
		}

		try {
			$invoice = $this->_initInvoice();
			if ($invoice) {

				if (!empty($data['capture_case'])) {
					$invoice->setRequestedCaptureCase($data['capture_case']);
				}

				if (!empty($data['comment_text'])) {
					$invoice->addComment(
					$data['comment_text'],
					isset($data['comment_customer_notify']),
					isset($data['is_visible_on_front'])
					);
				}

				$invoice->register();

				if (!empty($data['send_email'])) {
					$invoice->setEmailSent(true);
				}

				$invoice->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
				$invoice->getOrder()->setIsInProcess(true);

				$transactionSave = Mage::getModel('core/resource_transaction')
				->addObject($invoice)
				->addObject($invoice->getOrder());
				$shipment = false;
				if (!empty($data['do_shipment']) || (int) $invoice->getOrder()->getForcedDoShipmentWithInvoice()) {
					$shipment = $this->_prepareShipment($invoice);
					if ($shipment) {
						$shipment->setEmailSent($invoice->getEmailSent());
						$transactionSave->addObject($shipment);
					}
				}
				$transactionSave->save();

				if (isset($shippingResponse) && $shippingResponse->hasErrors()) {
					$this->_getSession()->addError($this->__('The invoice and the shipment  have been created. The shipping label cannot be created at the moment.'));
				} elseif (!empty($data['do_shipment'])) {
					$this->_getSession()->addSuccess($this->__('The invoice and shipment have been created.'));
				} else {
					$this->_getSession()->addSuccess($this->__('The invoice has been created.'));
				}

				// send invoice/shipment emails
				$comment = '';
				if (isset($data['comment_customer_notify'])) {
					$comment = $data['comment_text'];
				}
				try {
					$invoice->sendEmail(!empty($data['send_email']), $comment);
				} catch (Exception $e) {
					Mage::logException($e);
					$this->_getSession()->addError($this->__('Unable to send the invoice email.'));
				}
				if ($shipment) {
					try {
						$shipment->sendEmail(!empty($data['send_email']));
					} catch (Exception $e) {
						Mage::logException($e);
						$this->_getSession()->addError($this->__('Unable to send the shipment email.'));
					}
				}
				Mage::getSingleton('adminhtml/session')->getCommentText(true);
				$this->_redirect('*/sales_order/view', array('order_id' => $orderId));
			} else {
				$this->_redirect('*/*/new', array('order_id' => $orderId));
			}
			return;
		} catch (Mage_Core_Exception $e) {
			$this->_getSession()->addError($e->getMessage());
		} catch (Exception $e) {
			$this->_getSession()->addError($this->__('Unable to save the invoice.'));
			Mage::logException($e);
		}
		$this->_redirect('*/*/new', array('order_id' => $orderId));
	}
}
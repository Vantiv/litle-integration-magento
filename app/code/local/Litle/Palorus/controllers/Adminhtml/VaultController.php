<?php

class Litle_Palorus_Adminhtml_VaultController extends Mage_Adminhtml_Controller_Action
{

	/**
	 * Inits the customer from the request.
	 *
	 * @return boolean|Mage_Customer_Model_Customer
	 */
	protected function _initCustomer()
	{
		$customerId = $this->getRequest()->getParam('customer_id');
		if ($customerId) {
			$customer = Mage::getModel('customer/customer')->load($customerId);
			if ($customer->getId()) {
				return $customer;
			}
		}
		return false;
	}

	/**
	 * Deletes a stored crard.
	 */
	public function deleteCardAction ()
	{
		$customer = $this->_initCustomer();
		if ($customer) {
			$vaultId = $this->getRequest()->getParam('vault_id');
			$vault = Mage::getModel('palorus/vault')->load($vaultId);
			if ($vault->getId()) {
				try {
					$vault->delete();
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('palorus')->__('Stored card successfully deleted.'));
				} catch (Exception $e) {
					Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				}
			}
			else {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('palorus')->__('Stored card not found.'));
			}
			$this->_redirect('adminhtml/customer/edit', array('id' => $customer->getId(), 'tab' => 'litle_vault_tab'));
			return;
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('palorus')->__('Customer not found.'));
		$this->_redirect('adminhtml/customer');
	}

	/**
	 * ACL check.
	 *
	 * @return bool
	 */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('customer/manage');
	}
}

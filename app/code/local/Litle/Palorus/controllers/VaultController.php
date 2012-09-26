<?php
/**
 * Vault front end controller
 *
 * @author jholden
 *
 */
class Litle_Palorus_VaultController extends Mage_Core_Controller_Front_Action
{
	public function preDispatch()
	{
		parent::preDispatch();
		if (!$this->_getSession()->authenticate($this) || !Mage::helper('palorus')->isVaultEnabled()) {
			$this->setFlag('', 'no-dispatch', true);
		}
	}

	/**
	 * List vaulted cards
	 */
	public function indexAction()
	{
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();
	}

	/**
	 * @todo Display the edit form
	 *
	 */
// 	public function editAction()
// 	{
// 		$this->loadLayout();
// 		$this->_initLayoutMessages('customer/session');

// 		$navigationBlock = $this->getLayout()->getBlock('customer_account_navigation');
// 		if ($navigationBlock) {
// 			$navigationBlock->setActive('palorus/vault');
// 		}

// 		$this->renderLayout();
// 	}

	/**
	 * @todo Save the edit form
	 *
	 */
// 	public function editPostAction()
// 	{

// 	}

	/**
	 * Delete the card from our database
	 */
	public function deleteAction()
	{
		$vaultId = $this->getRequest()->getParam('vault_id');
		if ($vaultId) {
			$vault = Mage::getModel('palorus/vault')->load($vaultId);
			if ($vault->getCustomerId() != $this->_getSession()->getCustomer()->getId()) {
				$this->_getSession()->addError($this->__('The card does not belong to this customer.'));
				$this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
				return;
			}

			try {
				$vault->delete();
				$this->_getSession()->addSuccess($this->__('The card has been deleted.'));
			} catch (Exception $e) {
				$this->_getSession()->addException($e, $this->__('An error occurred while deleting the card.'));
				Mage::logException($e);
			}
		}
		$this->_redirect('*/*/index');
	}

	/**
	 * Retrieve customer session object
	 *
	 * @return Mage_Customer_Model_Session
	 */
	protected function _getSession()
	{
		return Mage::getSingleton('customer/session');
	}
}

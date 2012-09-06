<?php

/**
 * Litle Vault Info block
 *
 * @author jholden
 */
class Litle_Palorus_Block_Adminhtml_Customer_Edit_Tab_Vault
extends Mage_Adminhtml_Block_Template
implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('litle/customer/tab/vault.phtml');
	}

	/**
	 * Returns the registry customer.
	 *
	 * @return Mage_Customer_Model_Customer
	 */
	public function getCustomer()
	{
		return Mage::registry('current_customer');
	}

	/**
	 * Returns a collection of vaulted cards for the registry customer.
	 *
	 * @return Litle_Palorus_Model_Mysql4_Vault_Collection
	 */
	public function getStoredCards()
	{
		return Mage::getModel('palorus/vault')->getCollection()->addCustomerFilter($this->getCustomer());
	}

	/**
	 * URL to delete a stored card.
	 *
	 * @param Litle_Palorus_Model_Vault $card
	 * @return string
	 */
	public function getCardDeleteUrl(Litle_Palorus_Model_Vault $card)
	{
		$params = array(
				'customer_id' => $this->getCustomer()->getId(),
				'vault_id' => $card->getId()
		);
		return $this->getUrl('palorus/adminhtml_vault/deleteCard', $params);
	}

	/**
	 * Retrieve the label used for the tab relating to this block
	 *
	 * @return string
	 */
	public function getTabLabel()
	{
		return $this->__('Litle Stored Cards');
	}

	/**
	 * Retrieve the title used by this tab
	 *
	 * @return string
	 */
	public function getTabTitle()
	{
		return $this->__('Click here to view stored cards for this customer');
	}

	/**
	 * Determines whether to display the tab
	 * Add logic here to decide whether you want the tab to display
	 *
	 * @return bool
	 */
	public function canShowTab()
	{
		return true;
	}

	/**
	 * Stops the tab being hidden
	 *
	 * @return bool
	 */
	public function isHidden()
	{
		return false;
	}
}

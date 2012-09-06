<?php

class Litle_Palorus_Block_Vault_List extends Mage_Core_Block_Template
{

	/**
	 * Retrieve customer model
	 *
	 * @return Mage_Customer_Model_Customer
	 */
	public function getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	}

	/**
	 * Returns an array of stored cards.
	 *
	 * @return array
	 */
	public function getStoredCards()
	{
		if (!$this->hasData('stored_cards')) {
			$cards = Mage::getModel('palorus/vault')->visibleStoredCards($this->getCustomer()
				->getId());

			$this->setStoredCards($cards);
		}
		return $this->getData('stored_cards');
	}

	/**
	 *
	 * @return string
	 */
	public function getDeleteUrl()
	{
		return $this->getUrl('*/*/delete');
	}

/**
 *
 * @todo New card url
 * @return string
 */
	// public function getAddUrl()
	// {
	// return $this->getUrl('*/*/new');
	// }

/**
 *
 * @todo Edit card url
 * @param OnePica_AuthnetCim_Model_PaymentProfile $profile
 * @return string
 */
	// public function getEditUrl(OnePica_AuthnetCim_Model_PaymentProfile
	// $profile)
	// {
	// return $this->getUrl('*/*/edit', array(
	// 'profile_id' => $profile->getId()
	// ));
	// }
}

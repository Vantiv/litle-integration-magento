<?php

class Litle_Palorus_Model_Vault extends Mage_Core_Model_Abstract
{
	protected $_model = NULL;

	protected function _construct()
	{
		$this->_model = 'palorus/vault';
		$this->_init($this->_model);
	}

	/**
	 * Get unique credit cards for customer
	 *
	 * @param int $customerId
	 * @return Litle_Palorus_Model_Mysql4_Vault_Collection
	 */
	public function uniqueCreditCard($customerId)
	{
		/* @var $collection Litle_Palorus_Model_Mysql4_Vault_Collection */
		$collection = Mage::getModel('palorus/vault')
			->getCollection()
			->addFieldToFilter('customer_id', $customerId)
			->addFieldToFilter('last4', array('neq' => ''));

		$collection->getSelect()->group(array('token', 'type'));

		return $collection;
	}
}

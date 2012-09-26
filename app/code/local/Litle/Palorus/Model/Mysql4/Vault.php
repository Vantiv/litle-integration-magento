<?php

class Litle_Palorus_Model_Mysql4_Vault extends Mage_Core_Model_Mysql4_Abstract
{

	protected function _construct()
	{
		$this->_init('palorus/vault', 'vault_id');
	}

	/**
	 * Sets the created and modified date attributes.
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return Litle_Palorus_Model_Mysql4_Vault
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		if (! $object->getId()) {
			$object->setCreated(now());
		}
		$object->setUpdated(now());

		return parent::_beforeSave($object);
	}
}

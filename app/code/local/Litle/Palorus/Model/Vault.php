<?php

class Litle_Palorus_Model_Vault extends Mage_Core_Model_Abstract
{
	protected $_model = NULL;

	protected function _construct()
	{
		$this->_model = 'palorus/vault';
		$this->_init($this->_model);
	}

}
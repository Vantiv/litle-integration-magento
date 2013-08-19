<?php

class Litle_Palorus_Model_Avscid extends Mage_Core_Model_Abstract
{
	protected $_model = NULL;

	protected function _construct()
	{
		$this->_model = 'palorus/avscid';
		$this->_init($this->_model);
	}

}
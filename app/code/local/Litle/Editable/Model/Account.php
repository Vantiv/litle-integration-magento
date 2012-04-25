<?php

class Litle_Editable_Model_Account extends Mage_Core_Model_Abstract
{
	protected $_model = NULL;

	protected function _construct()
	{
		$this->_model = 'editable/account';
		$this->_init($this->_model);
	}

}
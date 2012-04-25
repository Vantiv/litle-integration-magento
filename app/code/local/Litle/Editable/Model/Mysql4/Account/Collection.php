<?php

class Litle_Editable_Model_Mysql4_Account_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected function _construct()
	{
		parent::_construct();
		$this->_init('editable/account');
	}

}
<?php

class Litle_Palorus_Model_Mysql4_Avscid_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected function _construct()
	{
		parent::_construct();
		$this->_init('palorus/avscid');
	}

}
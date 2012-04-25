<?php

  class Litle_Editable_Model_Mysql4_Account extends Mage_Core_Model_Mysql4_Abstract
  {
      protected function _construct()
      {
          $this->_init('editable/account', 'editable_account_id');
      }
  }
<?php

  class Litle_Palorus_Model_Mysql4_Vault extends Mage_Core_Model_Mysql4_Abstract
  {
      protected function _construct()
      {
          $this->_init('palorus/vault', 'vault_id');
      }
  }
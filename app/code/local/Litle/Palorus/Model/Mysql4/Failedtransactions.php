<?php

  class Litle_Palorus_Model_Mysql4_Failedtransactions extends Mage_Core_Model_Mysql4_Abstract
  {
      protected function _construct()
      {
          $this->_init('palorus/failedtransactions', 'failed_transactions_id');
      }
  }
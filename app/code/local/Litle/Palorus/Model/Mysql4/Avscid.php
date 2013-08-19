<?php

  class Litle_Palorus_Model_Mysql4_Avscid extends Mage_Core_Model_Mysql4_Abstract
  {
      protected function _construct()
      {
          $this->_init('palorus/avscid', 'avs_cid_id');
      }
  }
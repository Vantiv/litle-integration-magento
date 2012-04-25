<?php

  class Litle_Palorus_Model_Mysql4_Insight extends Mage_Core_Model_Mysql4_Abstract
  {
      protected function _construct()
      {
          $this->_init('palorus/insight', 'customer_insight_id');
      }
  }
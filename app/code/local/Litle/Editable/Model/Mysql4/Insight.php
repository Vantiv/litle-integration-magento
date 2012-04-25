<?php

  class Litle_Editable_Model_Mysql4_Insight extends Mage_Core_Model_Mysql4_Abstract
  {
      protected function _construct()
      {
          $this->_init('editable/insight', 'customer_insight_id');
      }
  }
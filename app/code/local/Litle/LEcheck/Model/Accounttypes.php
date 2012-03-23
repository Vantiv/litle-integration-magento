<?php
class Litle_LEcheck_Model_Accounttypes
{
    public function getAllowedTypes()
    {
        return array('Checking','Savings', 'Corporate', 'Corp Savings');
    }
    
	public function toOptionArray()
    {
        /**
         * making filter by allowed cards
         */
        $allowed = $this->getAllowedTypes();
        $options = array();

        foreach (Mage::getSingleton('lecheck/config')->getAccountTypes() as $code => $name) {
            if (in_array($code, $allowed) || !count($allowed)) {
                $options[] = array(
                   'value' => $code,
                   'label' => $name 
                );
            }
        }

        return $options;
    }
}
<?php
class Litle_LitleEcheck_Model_Accounttypes
{
    public function getAllowedTypes()
    {
        return array('CHECKING', 'BUSINESSCHECKING', 'SAVINGS');
    }
    
	public function toOptionArray()
    {
        /**
         * making filter by allowed cards
         */
        $allowed = $this->getAllowedTypes();
        $options = array();

        foreach (Mage::getSingleton('litleecheck/config')->getAccountTypes() as $code => $name) {
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
<?php
class Litle_LitleEcheck_Block_Form_LitleEcheck extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payment/form/echeck.phtml');
    }

    /**
     * Retrieve payment configuration object
     *
     * @return Mage_Payment_Model_Config
     */
//     protected function _getConfig()
//     {
//         return Mage::getSingleton('litleecheck/config');
//     }

    /**
     * Retrieve availables credit card types
     *
     * @return array
     */
    public function getAccountAvailableTypes()
    {
        return array('Checking', 'Savings', 'Corporate', 'Corp Savings');
    }
}
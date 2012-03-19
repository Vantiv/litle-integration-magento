<?php
class Litle_LitleEcheck_Block_Form_Echeck extends Mage_Payment_Block_Form
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
//         return Mage::getSingleton('echeck/config');
//     }

    /**
     * Retrieve availables credit card types
     *
     * @return array
     */
//     public function getAccountAvailableTypes()
//     {
//         $types = $this->_getConfig()->getAccountTypes();
//         if ($method = $this->getMethod()) {
//             $availableTypes = $method->getConfigData('accounttypes');
//             if ($availableTypes) {
//                 $availableTypes = explode(',', $availableTypes);
//                 foreach ($types as $code=>$name) {
//                     if (!in_array($code, $availableTypes)) {
//                         unset($types[$code]);
//                     }
//                 }
//             }
//         }
//         return $types;
//     }
}
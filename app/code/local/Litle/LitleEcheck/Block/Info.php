<?php
/**
 * Base payment iformation block
 *
 */
class Litle_LitleEcheck_Block_Info extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payment/info/default.phtml');
    }

    /**
     * Retrieve info model
     *
     * @return Mage_Payment_Model_Info
     */
    public function getInfo()
    {
        $info = $this->getData('info');
        if (!($info instanceof Oggetto_Echeck_Model_Info)) {
            Mage::throwException($this->__('Can not retrieve payment info model object.'));
        }
        return $info;
    }

    /**
     * Retrieve payment method model
     *
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function getMethod()
    {
        return $this->getInfo()->getMethodInstance();
    }
    
    public function toPdf()
    {
        $this->setTemplate('payment/info/pdf/default.phtml');
        return $this->toHtml();
    }
}
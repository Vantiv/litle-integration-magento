<?php
/**
 * Payment method form base block
 */
class Litle_LitleEcheck_Block_Form extends Mage_Core_Block_Template
{
    /**
     * Retrieve payment method model
     *
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function getMethod()
    {
        $method = $this->getData('method');

        if (!($method instanceof Mage_Payment_Model_Method_Abstract)) {
            Mage::throwException($this->__('Can not retrieve payment method model object.'));
        }
        return $method;
    }

    /**
     * Retrieve payment method code
     *
     * @return string
     */
    public function getMethodCode()
    {
        return $this->getMethod()->getCode();
    }

    /**
     * Retrieve field value data from payment info object
     *
     * @param   string $field
     * @return  mixed
     */
    public function getInfoData($field)
    {
        return $this->htmlEscape($this->getMethod()->getInfoInstance()->getData($field));
    }
}
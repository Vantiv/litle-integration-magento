<?php

class Litle_LPaypal_Block_System_Config_Form extends Mage_Adminhtml_Block_System_Config_Form
{
	/**
     * Init config group
     *
     * @param Varien_Data_Form $form
     * @param Varien_Simplexml_Element $group
     * @param Varien_Simplexml_Element $section
     * @param Varien_Data_Form_Element_Fieldset|null $parentElement
     */
    protected function _initGroup($form, $group, $section, $parentElement = null)
    {
        if ($group->frontend_model) {
            $fieldsetRenderer = Mage::getBlockSingleton((string)$group->frontend_model);
        } else {
            $fieldsetRenderer = $this->_defaultFieldsetRenderer;
        }

        $fieldsetRenderer->setForm($this)
            ->setConfigData($this->_configData);

        if ($this->_configFields->hasChildren($group, $this->getWebsiteCode(), $this->getStoreCode())) {
            $helperName = $this->_configFields->getAttributeModule($section, $group);
            $fieldsetConfig = array('legend' => Mage::helper($helperName)->__((string)$group->label));
            if (!empty($group->comment)) {
                $fieldsetConfig['comment'] = Mage::helper($helperName)->__((string)$group->comment);
            }
            if (!empty($group->expanded)) {
                $fieldsetConfig['expanded'] = (bool)$group->expanded;
            }

            $fieldset = new Varien_Data_Form_Element_Fieldset($fieldsetConfig);
            $fieldset->setId($section->getName() . '_' . $group->getName())
                ->setRenderer($fieldsetRenderer)
                ->setGroup($group);

            if ($parentElement) {
                $fieldset->setIsNested(true);
                $parentElement->addElement($fieldset);
            } else {

                $form->addElement($fieldset);
            }

            $this->_prepareFieldOriginalData($fieldset, $group);
            $this->_addElementTypes($fieldset);

            $this->_fieldsets[$group->getName()] = $fieldset;

            if ($group->clone_fields) {
                if ($group->clone_model) {
                    $cloneModel = Mage::getModel((string)$group->clone_model);
                } else {
                    Mage::throwException($this->__('Config form fieldset clone model required to be able to clone fields'));
                }
                foreach ($cloneModel->getPrefixes() as $prefix) {
                    $this->initFields($fieldset, $group, $section, $prefix['field'], $prefix['label']);                }
            } else {
                if($group->label == "Litle - Paypal" &&
                    (Mage::getStoreConfig('payment/paypal_express/payment_action') != 'Order' || !Mage::getStoreConfig('payment/paypal_express/active')))
                    $fieldset->addField('label', 'label', array('value'     => "Enable Paypal Express checkout and set payment action to 'Order' to use this feature.",));
                else
                    $this->initFields($fieldset, $group, $section);
            }
        }
    }
}

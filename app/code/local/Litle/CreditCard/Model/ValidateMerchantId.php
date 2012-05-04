<?php
class Litle_CreditCard_Model_ValidateMerchantId extends Mage_Core_Model_Config_Data
{
	public function getFieldsetDataValue($key)
	{
		$data = $this->_getData('fieldset_data');
		return (is_array($data) && isset($data[$key])) ? $data[$key] : null;
	}

	function save(){
		if ($this->getFieldsetDataValue('active'))
		{
			$currency = "USD";
			$string2Eval = 'return array' . $this->getFieldsetDataValue("merchant_id") . ';';
			$merchant_map = eval($string2Eval);

			if(!is_array($merchant_map)){
				Mage::throwException('Merchant ID must be of the form ("Currency" => "Code"), '. PHP_EOL . 'i.e. ("USD" => "101","GBP" => "102")');
			}
			if(empty($merchant_map[$currency])){
				Mage::throwException('Please Make sure that the Base Currency: ' . $currency . ' is in the Merchant ID Array');
			}

		}
		return parent::save();
	}
}

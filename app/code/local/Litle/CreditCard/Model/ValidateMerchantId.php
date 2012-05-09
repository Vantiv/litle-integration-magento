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
			$merchantId = $this->getFieldsetDataValue("merchant_id");
			Litle_CreditCard_Model_ValidateMerchantId::validate($merchantId);
		}
		return parent::save();
	}
	
	public static function validate($merchantId) {
		$string2Eval = 'return array' . $merchantId . ';';
		$currency = "USD";//assumed that the base currency is USD
		@$merchant_map = eval($string2Eval);
		
		if(!is_array($merchant_map)){
			Mage::throwException('Merchant ID must be of the form ("Currency" => "Code"), '. PHP_EOL . 'i.e. ("USD" => "101","GBP" => "102")');
		}
		if(empty($merchant_map[$currency])){
			Mage::throwException('Please Make sure that the Base Currency: ' . $currency . ' is in the Merchant ID Array');
		}
	}
}

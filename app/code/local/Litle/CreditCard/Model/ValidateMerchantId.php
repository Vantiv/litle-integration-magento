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
			//var_dump($this);exit;
				//$currency = $this->getFieldsetDataValue('currency');// = "UWD";
				$currency = "USD";
				$currency = Mage::getSingleton(creditcard/payment)
				//Mage::throwException($currency);
				$string2Eval = 'return array' . $this->getFieldsetDataValue("merchant_id") . ';';
				$merchant_map = eval($string2Eval);
					//Mage::throwException(get_class($merchant_map));
				if (!is_array($merchant_map) || empty($merchant_map[$currency])){
				
					Mage::throwException('Merchant ID must be of the form ("Currency" => "Code"), i.e. ("USD" => "101","GBP" => "102")');
				}
			}
			return parent::save();
		}
	}

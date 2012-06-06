<?php
class Litle_CreditCard_Model_Validatehttp extends Mage_Core_Model_Config_Data
{
	public function getFieldsetDataValue($key)
	{
		$data = $this->_getData('fieldset_data');
		return (is_array($data) && isset($data[$key])) ? $data[$key] : null;
	}
	
	public function getEcheckConfigData($fieldToLookFor, $store = NULL)
	{
		$returnFromThisModel = Mage::getStoreConfig('payment/LEcheck/' . $fieldToLookFor);
		if( $returnFromThisModel == NULL )
		$returnFromThisModel = parent::getConfigData($fieldToLookFor, $store);
	
		return $returnFromThisModel;
	}
	function save(){
		if ($this->getFieldsetDataValue('active') || $this->getEcheckConfigData('active'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_PROXY, $this->getFieldsetDataValue('proxy'));
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/xml'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, 'Test Connectivity');
			curl_setopt($ch, CURLOPT_URL, $this->getFieldsetDataValue('url'));
			curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
			curl_setopt($ch,CURLOPT_TIMEOUT,'5');
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$output = curl_exec($ch);

			if (! $output){
				Mage::throwException('Error connecting to Litle. Make sure your HTTP configuration settings are correct.');
			}
			else
			{
				curl_close($ch);
			}
			
			return parent::save();
		}
	}
}
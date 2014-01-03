<?php

class Litle_Palorus_Helper_Data extends Mage_Core_Helper_Abstract
{

	/**
	 *
	 * @param Mage_Payment_Model_Abstract $payment
	 * @param unknown_type $litleResponse
	 */
	public function saveCustomerInsight($payment, $litleResponse)
	{
		preg_match('/.*(\d\d\d\d)/', $payment->getCcNumber(), $matches);
		$last4 = $matches[1];
		$data = array(
				'customer_id' => $payment->getOrder()->getCustomerId(),
				'order_number' => XMLParser::getNode($litleResponse, 'orderId'),
				'order_id' => $payment->getOrder()->getId(),
				'affluence' => self::formatAffluence(XMLParser::getNode($litleResponse, 'affluence')),
				'last' => $last4,
				'order_amount' => self::formatAvailableBalance($payment->getAmountAuthorized()),
				'affluence' => self::formatAffluence(XMLParser::getNode($litleResponse, 'affluence')),
				'issuing_country' => XMLParser::getNode($litleResponse, 'issuerCountry'),
				'prepaid_card_type' => self::formatPrepaidCardType(
						XMLParser::getNode($litleResponse, 'prepaidCardType')),
				'funding_source' => self::formatFundingSource(XMLParser::getNode($litleResponse, 'type')),
				'available_balance' => self::formatAvailableBalance(
						XMLParser::getNode($litleResponse, 'availableBalance')),
				'reloadable' => self::formatReloadable(XMLParser::getNode($litleResponse, 'reloadable'))
		);
		Mage::getModel('palorus/insight')->setData($data)->save();
	}

	public function isVaultEnabled()
	{
		return Mage::getStoreConfig('payment/CreditCard/vault_enable');
	}

	public function getBaseUrl()
	{
		$url = Mage::getModel('creditcard/paymentLogic')->getConfigData('url');
		return self::getBaseUrlFrom($url);
	}

	static public function getBaseUrlFrom($url)
	{
		if (preg_match('/payments/', $url)) {
			$baseUrl = 'https://reports.litle.com';
		} else
			if (preg_match('/sandbox/', $url)) {
				$baseUrl = 'https://www.testlitle.com/sandbox';
			} else
				if (preg_match('/precert/', $url)) {
					$baseUrl = 'https://reports.precert.litle.com';
				} else
					if (preg_match('/cert/', $url)) {
						$baseUrl = 'https://reports.cert.litle.com';
					} else {
						$baseUrl = 'http://localhost:2190';
					}
		return $baseUrl;
	}

	/**
	 * Convert from Magento card types to Litle
	 *
	 * @deprecated
	 *
	 * @param Varien_Object $payment
	 * @return string
	 */
	public function litleCcTypeEnum(Varien_Object $payment)
	{
		return $this->litleCcType($payment->getCcType());
	}

	/**
	 * Convert from Magento card type to Litle
	 *
	 * @param unknown_type $type
	 * @return Ambigous <string, unknown>
	 */
	public function litleCcType($type)
	{
		$typeEnum = $type;
		if ($type == 'AE') {
			$typeEnum = 'AX';
		} elseif ($type == 'JCB') {
			$typeEnum = 'JC';
		}
		return $typeEnum;
	}

	/**
	 * Convert from Litle card types to Magento card types
	 *
	 * @param string $type
	 * @return string
	 */
	public function mageCcTypeLitle($type)
	{
		$typeEnum = $type;

		if ($type == 'AX') {
			$typeEnum = 'AE';
		} elseif ($type == 'JC') {
			$typeEnum = 'JCB';
		}

		return $typeEnum;
	}

	static public function formatAvailableBalance($balance)
	{
		if ($balance === '' || $balance === NULL) {
			$available_balance = '';
		} else {
			$balance = str_pad($balance, 3, '0', STR_PAD_LEFT);
			$available_balance = substr_replace($balance, '.', -2, 0);
			$available_balance = '$' . $available_balance; 
		}

		return $available_balance;
	}

	static public function formatAffluence($affluence)
	{
		if ($affluence === '' || $affluence === NULL) {
			return '';
		} else
			if ($affluence == 'AFFLUENT') {
				return 'Affluent';
			} else
				if ($affluence == 'MASS AFFLUENT') {
					return 'Mass Affluent';
				} else {
					return $affluence;
				}
	}

	static public function formatFundingSource($prepaid)
	{
		if ($prepaid == 'FSA') {
			return 'FSA';
		}
		return self::capitalize($prepaid);
	}

	static public function formatPrepaidCardType($prepaidCardType)
	{
		return self::capitalize($prepaidCardType);
	}

	static public function formatReloadable($reloadable)
	{
		return self::capitalize($reloadable);
	}

	static private function capitalize($original)
	{
		if ($original === '' || $original === NULL) {
			return '';
		}
		$lower = strtolower($original);
		return ucfirst($lower);
	}

	static private function formatMoney($balance)
	{
		if ($balance === '' || $balance === NULL) {
			$available_balance = '';
		} else {
			$available_balance = '$' . number_format($balance, 2);
		}

		return $available_balance;
	}
	
	/**
	* Returns the checkout session.
	*
	* @return Mage_Core_Model_Session_Abstract
	*/
	public function getCheckout()
	{
		if (Mage::app()->getStore()->isAdmin()) {
			return Mage::getSingleton('adminhtml/session_quote');
		} else {
			return Mage::getSingleton('checkout/session');
		}
	}
	
	/**
	 * Returns the quote.
	 *
	 * @return Mage_Sales_Model_Quote
	 */
	public function getQuote()
	{
		return $this->getCheckout()->getQuote();
	}
	
	/**
	 * Returns the logged in user.
	 *
	 * @return Mage_Customer_Model_Customer
	 */
	public function getCustomer()
	{
		return $this->getQuote()->getCustomer();
	}
}

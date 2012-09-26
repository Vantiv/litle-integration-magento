<?php

/**
 * Magento
 * NOTICE OF LICENSE
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the file LICENSE.txt. It is also available
 * through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php If you did not receive a copy of
 * the license and are unable to obtain it through the world-wide-web, please
 * send an email to license@magentocommerce.com so we can send you a copy
 * immediately.
 * DISCLAIMER
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category Mage
 * @package Mage_Payment
 * @copyright Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License
 *          (OSL 3.0)
 */
class Litle_CreditCard_Block_Form_CreditCard extends Mage_Payment_Block_Form
{

	/**
	 *
	 * @var array
	 */
	protected $_storedCards = null;

	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('litle/form/litlecc.phtml');
	}

	/**
	 * Retrieve payment configuration object
	 *
	 * @return Mage_Payment_Model_Config
	 */
	protected function _getConfig()
	{
		return Mage::getSingleton('payment/config');
	}


	public function getCurrency()
	{
		return Mage::app()->getStore()->getCurrentCurrencyCode();
	}

	public function getMerchantIdMap()
	{
		return Mage::getStoreConfig('payment/CreditCard/merchant_id');
	}

	public function getReportGroup()
	{
		$string2Eval = 'return array' . $this->getMerchantIdMap() . ";";
		$merchant_map = eval($string2Eval);
		$reportGroup = $merchant_map[$this->getCurrency()];
		return $reportGroup;
	}

	/**
	 * Retrieve availables credit card types
	 *
	 * @return array
	 */
	public function getCcAvailableTypes()
	{
		$types = $this->_getConfig()->getCcTypes();
		if ($method = $this->getMethod()) {
			$availableTypes = $method->getConfigData('cctypes');
			if ($availableTypes) {
				$availableTypes = explode(',', $availableTypes);
				foreach ($types as $code => $name) {
					if (! in_array($code, $availableTypes)) {
						unset($types[$code]);
					}
				}
			}
		}
		return $types;
	}

	/**
	 * Retrieve credit card expire months
	 *
	 * @return array
	 */
	public function getCcMonths()
	{
		$months = $this->getData('cc_months');
		if (is_null($months)) {
			$months[0] = $this->__('Month');
			$months = array_merge($months, $this->_getConfig()->getMonths());
			$this->setData('cc_months', $months);
		}
		return $months;
	}

	/**
	 * Retrieve credit card expire years
	 *
	 * @return array
	 */
	public function getCcYears()
	{
		$years = $this->getData('cc_years');
		if (is_null($years)) {
			$years = $this->_getConfig()->getYears();
			$years = array(
					0 => $this->__('Year')
			) + $years;
			$this->setData('cc_years', $years);
		}
		return $years;
	}

	/**
	 * Retrive has verification configuration
	 *
	 * @return boolean
	 */
	public function hasVerification()
	{
		if ($this->getMethod()) {
			$configData = $this->getMethod()->getConfigData('useccv');
			if (is_null($configData)) {
				return true;
			}
			return (bool) $configData;
		}
		return true;
	}

	/* Whether switch/solo card type available */
	public function hasSsCardType()
	{
		$availableTypes = explode(',', $this->getMethod()->getConfigData('cctypes'));
		$ssPresenations = array_intersect(array(
				'SS',
				'SM',
				'SO'
		), $availableTypes);
		if ($availableTypes && count($ssPresenations) > 0) {
			return true;
		}
		return false;
	}

	/* solo/switch card start year @return array */
	public function getSsStartYears()
	{
		$years = array();
		$first = date("Y");

		for ($index = 5; $index >= 0; $index --) {
			$year = $first - $index;
			$years[$year] = $year;
		}
		$years = array(
				0 => $this->__('Year')
		) + $years;
		return $years;
	}

	public function getPaypageEnabled()
	{
		return Mage::getStoreConfig('payment/CreditCard/paypage_enable');
	}

	public function getVaultEnabled()
	{
		return Mage::helper('palorus')->isVaultEnabled();
	}

	/**
	 *
	 * @return Litle_Palorus_Model_Mysql4_Vault_Collection
	 */
	public function getStoredCards()
	{
		if (is_null($this->_storedCards)) {
			$this->_storedCards = Mage::getModel('palorus/vault')->getCollection()->addCustomerFilter(Mage::helper('palorus')->getCustomer());
		}
		return $this->_storedCards;
	}

	public function hasStoredCards()
	{
		if (count($this->getStoredCards())) {
			return true;
		}
		return false;
	}

	/**
	 * Render block HTML
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		Mage::dispatchEvent('payment_form_block_to_html_before', array(
				'block' => $this
		));
		return parent::_toHtml();
	}
}


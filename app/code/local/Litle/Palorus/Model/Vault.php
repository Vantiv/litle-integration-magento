<?php

class Litle_Palorus_Model_Vault extends Mage_Core_Model_Abstract
{
	protected $_model = NULL;

	protected function _construct()
	{
		$this->_model = 'palorus/vault';
		$this->_init($this->_model);
	}

	/**
	 * Get unique credit cards for customer
	 *
	 * @param int $customerId
	 * @return Litle_Palorus_Model_Mysql4_Vault_Collection
	 */
	public function visibleStoredCards($customerId)
	{
		/* @var $collection Litle_Palorus_Model_Mysql4_Vault_Collection */
		return Mage::getModel('palorus/vault')
			->getCollection()
			->addFieldToFilter('is_visible', 1)
			->addFieldToFilter('customer_id', $customerId);
	}

	/**
	 * Get a matching customer vault item.
	 *
	 * @param Mage_Customer_Model_Customer $customer
	 * @param string $token
	 * @return Litle_Palorus_Model_Vault
	 */
	public function getCustomerToken(Mage_Customer_Model_Customer $customer, $token)
	{
		$c = $this->getCollection()->addCustomerFilter($customer)
			->addFieldToFilter('token', $token);

		if ($c->count()) {
			return $c->getFirstItem();
		}
		return null;
	}

	/**
	 * Create or update a token from a payment object
	 *
	 * @param Varien_Object $payment
	 * @param string $vault
	 * @param string $bin
	 * @return Litle_Palorus_Model_Vault
	 */
	public function setTokenFromPayment(Varien_Object $payment, $token, $bin)
	{
		if (!$payment->getCcNumber() || !$token) {
			return false;
		}

		$vault = $this->getCustomerToken($payment->getOrder()->getCustomer(), $token);
		if (!$vault) {
			$vault = Mage::getModel('palorus/vault');
		}

		$order = $payment->getOrder();
		Mage::helper('core')->copyFieldset('palorus_vault_order', 'to_vault', $order, $vault);
		Mage::helper('core')->copyFieldset('palorus_vault_payment', 'to_vault', $payment, $vault);

		
		$last4 = substr($payment->getCcNumber(), -4);
		$ccType = $payment->getCcType();
		
		
		
		$vault->setLast4(substr($payment->getCcNumber(), -4))
			->setLitleCcType($payment->getCcType())
			->setToken($token)
			->setBin($bin);

		$vault->save();

		$order->setLitleVaultId($vault->getId());

		return $vault;
	}
	
	/**
	 * Create a token with the minimum information.
	 *
	 * @param Mage_Customer_Model_Customer $customer
	 * @param string $token
	 * @param string $bin
	 * @param string $type
	 * @param int $expMonth
	 * @param int $expYear
	 * @param boolean $isVisible
	 * @return Litle_Palorus_Model_Vault
	 */
	public function createBasicToken(Mage_Customer_Model_Customer $customer, $token, $bin, $type, $expMonth, $expYear, $isVisible = true)
	{
		$vault = $this->getCustomerToken($customer, $token);
		if (!$vault) {
			$vault = Mage::getModel('palorus/vault');
		}

		$vault->setCustomerId($customer->getId())
			->setToken($token)
			->setBin($bin)
			->setCcType($type)
			->setExpirationMonth($expMonth)
			->setExpirationYear($expYear)
			->setIsVisible($isVisible)
			->save();

		return $vault;
	}

	public function setLitleCcType($code)
	{
		$this->setType($code);
		return $this;
	}

	public function setCcType($code)
	{
		$this->setType(Mage::helper('palorus')->litleCcType($code));
		return $this;
	}

	public function getCcType()
	{
		return Mage::helper('palorus')->mageCcTypeLitle($this->getType());
	}

	public function getLitleCcType()
	{
		return $this->getType();
	}

	/**
	 * Get the human-friendly card type
	 *
	 * @return string
	 */
	public function getTypeName()
	{
		if ($this->getType()) {
			$type = $this->getCcType();
			$types = Mage::getSingleton('payment/config')->getCcTypes();

			if (array_key_exists($type, $types)) {
				return $types[$type];
			}
			return $type;
		}
		return '';
	}
}

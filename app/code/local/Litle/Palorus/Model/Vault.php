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

		$vault->setLast4(substr($payment->getCcNumber(), -4))
			->setType(Mage::helper('palorus')->litleCcTypeEnum($payment))
			->setToken($token)
			->setBin($bin)
			->setOrderType($payment->getInfoInstance()->getAdditionalInformation('orderSource'));

		$vault->save();

		$order->setLitleVaultId($vault->getId());

		return $vault;
	}

	/**
	 * Get the human-friendly card type
	 *
	 * @return string
	 */
	public function getTypeName()
	{
		if ($this->getType()) {
			$type = Mage::helper('palorus')->mageCcTypeLitle($this->getType());
			$types = Mage::getSingleton('payment/config')->getCcTypes();

			if (array_key_exists($type, $types)) {
				return $types[$type];
			}
			return $type;
		}
		return '';
	}
}

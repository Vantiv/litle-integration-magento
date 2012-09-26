<?php
/**
 * Stored card rendering block
 */
class Litle_Palorus_Block_Vault extends Mage_Core_Block_Abstract
{
	/**
	 * @var array
	 */
	protected $_params = array();

	/**
	 * Renders the block.
	 *
	 * @return string
	 */
	protected function _toHtml ()
	{
		if ($this->getPaymentProfile() && $this->getType()) {
			switch ($this->getType()) {
				case 'oneline':
					return $this->_getOneline();
				case 'json':
					return $this->_getJson();
				case 'html':
					return $this->_getHtml();
			}
		}
		return '';
	}

	/**
	 * Sets rendering params.
	 *
	 * Supported params:
	 *	show_exp_date
	 *  container_tag
	 *
	 * @param array $params
	 * @return Litle_Palorus_Block_Vault
	 */
	public function setParams (array $params)
	{
		$this->_params = $params;
		return $this;
	}

	/**
	 * Param getter.
	 *
	 * @param string $param
	 * @return mixed
	 */
	public function getParam ($param)
	{
		return isset($this->_params[$param]) ? $this->_params[$param] : false;
	}

	/**
	 * @return string
	 */
	protected function _getOneline ()
	{
		$profile = $this->getPaymentProfile();
		$str = Mage::helper('palorus')->__(
			'Card Type: %s, xxxx-%s, Exp: %s/%s',
			$profile->getType(),
			$profile->getLast4(),
			$profile->getExpirationMonth(),
			$profile->getExpirationYear()
		);
		return $str;
	}

	/**
	 * @return string
	 */
	protected function _getJson ()
	{
		$profile = $this->getPaymentProfile();
		return Mage::helper('core')->jsonEncode($profile->getData());
	}

	/**
	 * @return string
	 */
	protected function _getHtml ()
	{
		$profile = $this->getPaymentProfile();
		$tag = $this->getParam('container_tag') ? $this->getParam('container_tag') : 'address';

		$str = '<' . $tag . '>';
		if ($profile->getCardType()) {
			$str .= Mage::helper('palorus')->__('Card Type: %s<br />', $profile->getType());
		}
		$str .= Mage::helper('palorus')->__('Card Number: XXXX-%s<br />', $profile->getLast4());

		if ($this->getParam('show_exp_date')) {
			$str .= Mage::helper('palorus')->__('Expiration: %s/%s<br />', $profile->getExpirationMonth(), $profile->getExpirationYear());
		}
		$str = $str . '</' . $tag . '>';

		return $str;
	}
}

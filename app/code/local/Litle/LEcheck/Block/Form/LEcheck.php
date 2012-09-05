<?php
class Litle_LEcheck_Block_Form_LEcheck extends Mage_Payment_Block_Form
{
	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('litle/form/litleecheck.phtml');
	}

	public function getAccountAvailableTypes()
	{
		$types = array('Checking' => 'Checking', 'Savings' => 'Savings','Corporate'=>'Corporate','Corp Savings' => 'Corp Savings');
		if ($method = $this->getMethod()) {
			$availableTypes = $method->getConfigData('accounttypes');
			if ($availableTypes) {
				$availableTypes = explode(',', $availableTypes);
				foreach ($types as $code=>$name) {
					if (!in_array($code, $availableTypes)) {
						unset($types[$code]);
					}
				}
			}
		}
		return $types;
	}
}

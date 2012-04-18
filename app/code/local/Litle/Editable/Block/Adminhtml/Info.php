<?php
/**
 * Magento Plieninger Editable Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 *
 * @category   Plieninger
 * @package    Plieninger_Editable
 * @copyright  Copyright (c) 2009 Andreas Plieninger
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL)
 * @author     Andreas Plieninger <aplieninger@gmx.de> www.plieninger.org
 * @version    0.1.0
 * @date       18.12.2009
*/


class Litle_Editable_Block_Adminhtml_Info extends Mage_Adminhtml_Block_Sales_Order_View_Info {
	public function __construct() {
		parent::__construct();
		$this->setTemplate('litle/editable.phtml');
	}

	protected function _beforeToHtml() {
		parent::_beforeToHtml();
		$this->setTemplate('litle/editable.phtml');
	}

	public function prepareAddressBlock($address) {
		$german = array(
			'Prefix',
			'Vorname',
			'Mittelname',
			'Nachname',
			'Suffix',
			'Firma',
			'Straße',
			'Stadt',
			'Region',
			'PLZ',
			'Land',
			'Tel.',
			'Fax',
		);

		$translation = array(
			$this->__('prefix'),
			$this->__('firstname'),
			$this->__('middlename'),
			$this->__('lastname'),
			$this->__('suffix'),
			$this->__('company'),
			$this->__('street'),
			$this->__('city'),
			$this->__('region'),
			$this->__('postcode'),
			$this->__('country'),
			$this->__('phone'),
			$this->__('fax'),
		);
		$address = str_replace($german,$translation,$address);


		$editableOptions = array(
			'okText' => $this->__('ok'),
			'cancelText' => $this->__('cancel'),
			//'textBeforeControls' => $this->__('text before'),
			//'textBetweenControls' => $this->__('text between'),
			//'textAfterControls' => $this->__('text after'),
			'savingText' => $this->__('Saving...'),
			'clickToEditText' => $this->__('Click to edit'),
		);

		$editableOptionsString = '';
		foreach($editableOptions as $key => $value) {
			if($editableOptionsString != '')
				$editableOptionsString .= ',';
			$editableOptionsString .= $key.':'."'$value'";
		}
		$address = str_replace('inplaceeditor_editable_options',$editableOptionsString,$address);
		return $address;
	}
}
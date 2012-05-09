<?php
/*
 * Copyright (c) 2011 Litle & Co.
*
* Permission is hereby granted, free of charge, to any person
* obtaining a copy of this software and associated documentation
* files (the "Software"), to deal in the Software without
* restriction, including without limitation the rights to use,
* copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the
* Software is furnished to do so, subject to the following
* conditions:
*
* The above copyright notice and this permission notice shall be
* included in all copies or substantial portions of the Software.
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND
* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
* OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
* NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
* HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
* WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
* FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
* OTHER DEALINGS IN THE SOFTWARE.
*/
require_once(getenv('MAGENTO_HOME')."/app/Mage.php");
require_once(getenv('MAGENTO_HOME')."/app/code/core/Mage/Core/Block/Abstract.php");
require_once(getenv('MAGENTO_HOME')."/app/code/core/Mage/Core/Block/Template.php");
require_once(getenv('MAGENTO_HOME')."/app/code/core/Mage/Adminhtml/Block/Template.php");
require_once(getenv('MAGENTO_HOME')."/app/code/core/Mage/Adminhtml/Block/Widget/Container.php");
require_once(getenv('MAGENTO_HOME')."/app/code/core/Mage/Adminhtml/Block/Sales/Transactions/Detail.php");
require_once(getenv('MAGENTO_HOME')."/app/code/local/Litle/CreditCard/Model/PaymentLogic.php");

class ValidateMerchantIdTest extends PHPUnit_Framework_TestCase
{

	public function testValidateMerchantId_Success() {
		//Should not throw an exception
		Litle_CreditCard_Model_ValidateMerchantId::validate("('USD'=>'101')");
		Litle_CreditCard_Model_ValidateMerchantId::validate("('USD'=>'101', 'CDN'=>'102')");
	}

	/**
	 * @dataProvider providerNotArray
	 */
	public function testValidateMerchantId_Fail($input) {
		$this->setExpectedException('Mage_Core_Exception', 'Merchant ID must be of the form ("Currency" => "Code")');
		Litle_CreditCard_Model_ValidateMerchantId::validate($input);
	}

	public function providerNotArray() {
		return array(
		array('MERCHANT'),
		array('101'),
		array('USD => 101'),
		array("'USD' => '101'"),
		);
	}

	/**
	 * @dataProvider providerBaseCurrencyNotInMap
	 */
	public function testBaseCurrencyNotInMap($input) {
		$this->setExpectedException('Mage_Core_Exception', 'Please Make sure that the Base Currency: USD is in the Merchant ID Array');
		Litle_CreditCard_Model_ValidateMerchantId::validate($input);
	}

	public function providerBaseCurrencyNotInMap() {
		return array(
		array("('CDN'=>'101')"),
		);
	}


}

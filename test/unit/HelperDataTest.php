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

class HelperDataTest extends PHPUnit_Framework_TestCase
{
	
	/**
	 * @dataProvider providerFormatAvailableBalance
	 */
	public function testFormatAvailableBalance($input, $expected) {
		$this->assertEquals($expected, Litle_Palorus_Helper_Data::formatAvailableBalance($input));
	}
	
	public function providerFormatAvailableBalance() {
		return array(
			array('2000','$20.00'),
			array('0','$0.00'),
			array('',''),
			array(NULL,""),
			array('10',"$0.10")						
		);
	}
	
	/**
	 * @dataProvider providerFormatAffluence
	 */
	public function testFormatAffluence($input, $expected) {
		$this->assertEquals($expected, Litle_Palorus_Helper_Data::formatAffluence($input));
	}
	
	public function providerFormatAffluence() {
		return array(
			array('AFFLUENT',"Affluent"),
			array('MASS AFFLUENT',"Mass Affluent"),
			array('',''),
			array(NULL,'')
		);
	}
	
	/**
	* @dataProvider providerFormatFundingSource
	*/
	public function testFormatPrepaid($input, $expected) {
		$this->assertEquals($expected, Litle_Palorus_Helper_Data::formatFundingSource($input));
	}
	
	public function providerFormatFundingSource() {
		return array(
		array('UNKNOWN',"Unknown"),
		array('PREPAID',"Prepaid"),
		array('FSA',"FSA"),
		array('CREDIT',"Credit"),
		array('DEBIT',"Debit"),
		array('',''),
		array(NULL,'')
		);
	}
	
	/**
	* @dataProvider providerFormatPrepaidCardType
	*/
	public function testFormatPrepaidCardType($input, $expected) {
		$this->assertEquals($expected, Litle_Palorus_Helper_Data::formatPrepaidCardType($input));
	}
	
	public function providerFormatPrepaidCardType() {
		return array(
		array('GIFT',"Gift"),
		array('',''),
		array(NULL,'')
		);
	}
	
	/**
	* @dataProvider providerFormatReloadable
	*/
	public function testFormatReloadable($input, $expected) {
		$this->assertEquals($expected, Litle_Palorus_Helper_Data::formatReloadable($input));
	}
	
	public function providerFormatReloadable() {
		return array(
		array('NO',"No"),
		array('YES',"Yes"),
		array('UNKNOWN',"Unknown"),
		array('',''),
		array(NULL,'')
		);
	}
	
	/**
	* @dataProvider providerFormatAmount
	*/
	public function testFormatAmount($input1, $input2, $expected) {
		$this->assertEquals($expected, Litle_CreditCard_Helper_Data::formatAmount($input1, $input2));
	}
	
	public function providerFormatAmount() {
		return array(
		array('10.00', true, '1000'),
		array('10.10', true, '1010'),
		array('10.101', true, '1011'),
		array(NULL, true, ""),
		array('', true, ""),
		array('10.00', false, '1000'),
		array('10.10', false, '1010'),
		array('10.101', false, '1010'),
		array(NULL, false, ""),
		array('', false, "")
		);
	}
}

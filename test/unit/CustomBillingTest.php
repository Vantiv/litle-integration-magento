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
require_once(getenv('MAGENTO_HOME')."/app/code/local/Litle/Palorus/Block/Adminhtml/Transaction.php");

class CustomBillingTest extends PHPUnit_Framework_TestCase
{
	public function testCustomBilling1()
	{		
		$url = Litle_CreditCard_Model_PaymentLogic::getCustomBilling('https://www.testing123.org');
		$this->assertEquals('testing123', $url['url']);
	}
	
	public function testCustomBilling2()
	{
		$url = Litle_CreditCard_Model_PaymentLogic::getCustomBilling('http://www.othertesturl.orders.tw/payments');
		$this->assertEquals('othertesturl', $url['url']);
	}
	
	public function testCustomBilling3()
	{
		$url = Litle_CreditCard_Model_PaymentLogic::getCustomBilling('http://www.othertesturl.orders.tw/payments/index.html');
		$this->assertEquals('othertesturl', $url['url']);
	}
	
	public function testCustomBilling4()
	{
		$url = Litle_CreditCard_Model_PaymentLogic::getCustomBilling('12345678912345');
		$this->assertEquals('1234567891234', $url['url']);
	}
	
	public function testCustomBilling5()
	{
		$url = Litle_CreditCard_Model_PaymentLogic::getCustomBilling('www.test.com/payments');
		$this->assertEquals('www.test.com', $url['url']);
	}
	
	public function testCustomBilling6()
	{
		$url = Litle_CreditCard_Model_PaymentLogic::getCustomBilling('www.longerlongerlonlonger.tv');
		$this->assertEquals('longerlongerl', $url['url']);
	}

}

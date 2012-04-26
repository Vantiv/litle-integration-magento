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

class TransactionTest extends PHPUnit_Framework_TestCase
{
	public function testProductionAuth()
	{		
 		$html = Litle_Palorus_Block_Adminhtml_Transaction::_getTxnIdHtml('authorization','creditcard','https://payments.litle.com/vap/communicator/online',123);
 		$this->assertEquals("<a href='https://reports.litle.com/ui/reports/payments/authorization/123'>123</a>",$html);
	}
	
	public function testProductionVerification()
	{
		$html = Litle_Palorus_Block_Adminhtml_Transaction::_getTxnIdHtml('authorization','lecheck','https://payments.litle.com/vap/communicator/online',123);
		$this->assertEquals("<a href='https://reports.litle.com/ui/reports/payments/echeck/verification/123'>123</a>",$html);
	}
	
	public function testProductionCapture()
	{
		$html = Litle_Palorus_Block_Adminhtml_Transaction::_getTxnIdHtml('capture','creditcard','https://payments.litle.com/vap/communicator/online',123);
		$this->assertEquals("<a href='https://reports.litle.com/ui/reports/payments/deposit/123'>123</a>",$html);
	}
	
	public function testProductionEcheckCapture()
	{
		$html = Litle_Palorus_Block_Adminhtml_Transaction::_getTxnIdHtml('capture','lecheck','https://payments.litle.com/vap/communicator/online',123);
		$this->assertEquals("<a href='https://reports.litle.com/ui/reports/payments/echeck/deposit/123'>123</a>",$html);
	}
	
	public function testProductionRefund()
	{
		$html = Litle_Palorus_Block_Adminhtml_Transaction::_getTxnIdHtml('refund','creditcard','https://payments.litle.com/vap/communicator/online',123);
		$this->assertEquals("<a href='https://reports.litle.com/ui/reports/payments/refund/123'>123</a>",$html);
	}
	
	public function testProductionEcheckRefund()
	{
		$html = Litle_Palorus_Block_Adminhtml_Transaction::_getTxnIdHtml('refund','lecheck','https://payments.litle.com/vap/communicator/online',123);
		$this->assertEquals("<a href='https://reports.litle.com/ui/reports/payments/echeck/refund/123'>123</a>",$html);
	}
	
	public function testProductionVoid()
	{
		$html = Litle_Palorus_Block_Adminhtml_Transaction::_getTxnIdHtml('void','creditcard','https://payments.litle.com/vap/communicator/online',"123456789012345678-void");
		$this->assertEquals("<a href='https://reports.litle.com/ui/reports/payments/authorization/reversal/123456789012345678'>123456789012345678-void</a>",$html);
	}
	
	public function testProductionEcheckVoid()
	{
		$html = Litle_Palorus_Block_Adminhtml_Transaction::_getTxnIdHtml('void','lecheck','https://payments.litle.com/vap/communicator/online',"123456789012345678");
		$this->assertEquals(null,$html);
	}
	
	public function testNotALitleTxn()
	{
		$html = Litle_Palorus_Block_Adminhtml_Transaction::_getTxnIdHtml('authorization','Authorize.netcreditcard','https://payments.litle.com/vap/communicator/online',123);
		$this->assertEquals(null,$html);
	}
	
	public function testCert()
	{
		$html = Litle_Palorus_Block_Adminhtml_Transaction::_getTxnIdHtml('authorization','creditcard','https://cert.litle.com/vap/communicator/online',123);
		$this->assertEquals("<a href='https://reports.cert.litle.com/ui/reports/payments/authorization/123'>123</a>",$html);
	}

	public function testPrecert()
	{
		$html = Litle_Palorus_Block_Adminhtml_Transaction::_getTxnIdHtml('authorization','creditcard','https://precert.litle.com/vap/communicator/online',123);
		$this->assertEquals("<a href='https://reports.precert.litle.com/ui/reports/payments/authorization/123'>123</a>",$html);
	}
	
	public function testSandbox()
	{
		$html = Litle_Palorus_Block_Adminhtml_Transaction::_getTxnIdHtml('authorization','creditcard','https://www.testlitle.com/sandbox/communicator/online',123);
		$this->assertEquals("<a href='https://www.testlitle.com/sandbox/ui/reports/payments/authorization/123'>123</a>",$html);
	}
	
	public function testLocal()
	{
		$html = Litle_Palorus_Block_Adminhtml_Transaction::_getTxnIdHtml('authorization','creditcard','http://localhost:2180/vap/communicator/online',123);
		$this->assertEquals("<a href='http://localhost:2190/ui/reports/payments/authorization/123'>123</a>",$html);
	}
	

}

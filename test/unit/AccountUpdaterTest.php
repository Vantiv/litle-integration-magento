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

class AccountUpdaterTest extends PHPUnit_Framework_TestCase
{
	
	protected $xmlNewCard;
	protected $domNewCard;
	protected $xmlNoNewCard;
	protected $domNoNewCard;
	protected $xmlNewToken;
	protected $domNewToken;
	
	protected function setUp()
	{
		
		$this->xmlNewCard = "<litleOnlineResponse version='8.12' response='0' message='Valid Format' xmlns='http://www.litle.com/schema'>
  <authorizationResponse id='' reportGroup='101' customerId='jg@litle.com'>
    <litleTxnId>701777775456002000</litleTxnId>
    <orderId>100000075</orderId>
    <response>000</response>
    <responseTime>2012-05-10T13:52:46</responseTime>
    <postDate>2012-05-10</postDate>
    <message>Approved</message>
    <authCode>62543</authCode>
    <accountUpdater>
      <originalCardInfo>
        <type>VI</type>
        <number>4000162019882000</number>
        <expDate>1110</expDate>
      </originalCardInfo>
      <newCardInfo>
        <type>VI</type>
        <number>4076490213412164</number>
        <expDate>1114</expDate>
      </newCardInfo>
    </accountUpdater>
  </authorizationResponse>
</litleOnlineResponse>";

		$this->domNewCard = new DOMDocument();
		$this->domNewCard->loadXML($this->xmlNewCard);
		
		$this->xmlNoNewCard = "<litleOnlineResponse version='8.12' response='0' message='Valid Format' xmlns='http://www.litle.com/schema'>
		  <authorizationResponse id='' reportGroup='101' customerId='jg@litle.com'>
		    <litleTxnId>701777775456002000</litleTxnId>
		    <orderId>100000075</orderId>
		    <response>000</response>
		    <responseTime>2012-05-10T13:52:46</responseTime>
		    <postDate>2012-05-10</postDate>
		    <message>Approved</message>
		    <authCode>62543</authCode>
		  </authorizationResponse>
		</litleOnlineResponse>";
		
		$this->domNoNewCard = new DOMDocument();
		$this->domNoNewCard->loadXML($this->xmlNoNewCard);
		
		$this->xmlNewToken = "<litleOnlineResponse version='8.12' response='0' message='Valid Format' xmlns='http://www.litle.com/schema'>
		  <authorizationResponse id='' reportGroup='101' customerId='jg@litle.com'>
		    <litleTxnId>701777775456002000</litleTxnId>
		    <orderId>100000075</orderId>
		    <response>000</response>
		    <responseTime>2012-05-10T13:52:46</responseTime>
		    <postDate>2012-05-10</postDate>
		    <message>Approved</message>
		    <authCode>62543</authCode>
		    <accountUpdater>
		      <originalCardTokenInfo>
		        <type>VI</type>
		        <litleToken>4000162019882000</litleToken>
		        <expDate>1110</expDate>
		      </originalCardTokenInfo>
		      <newCardTokenInfo>
		        <type>VI</type>
		        <litleToken>4076490213412164</litleToken>
		        <expDate>1114</expDate>
		      </newCardTokenInfo>
		    </accountUpdater>
		  </authorizationResponse>
		</litleOnlineResponse>";
		
		$this->domNewToken = new DOMDocument();
		$this->domNewToken->loadXML($this->xmlNewToken);
	}

	public function testAccountUpdater1()
	{
		$element = Litle_CreditCard_Model_PaymentLogic::getUpdater($this->domNewCard, 'newCardInfo', 'number');
		$this->assertEquals('4076490213412164', $element);
	}
	
	public function testAccountUpdater2()
	{
		$element = Litle_CreditCard_Model_PaymentLogic::getUpdater($this->domNewCard, 'newCardInfo', 'type');
		$this->assertEquals('VI', $element);
	}
	
	public function testAccountUpdater3()
	{
		$element = Litle_CreditCard_Model_PaymentLogic::getUpdater($this->domNewCard, 'newCardInfo', 'expDate');
		$this->assertEquals('1114', $element);
	}
	
	public function testAccountUpdater4()
	{
		$element = Litle_CreditCard_Model_PaymentLogic::getUpdater($this->domNoNewCard, 'newCardInfo');
		$this->assertTrue($element === NULL);
	}
	
	public function testAccountUpdater5()
	{
		$element = Litle_CreditCard_Model_PaymentLogic::getUpdater($this->domNewToken, 'newCardTokenInfo', 'litleToken');
		$this->assertEquals('4076490213412164', $element);
	}
	
	public function testAccountUpdater6()
	{
		$element = Litle_CreditCard_Model_PaymentLogic::getUpdater($this->domNewToken, 'newCardTokenInfo', 'type');
		$this->assertEquals('VI', $element);
	}
	
	public function testAccountUpdater7()
	{
		$element = Litle_CreditCard_Model_PaymentLogic::getUpdater($this->domNewToken, 'newCardTokenInfo', 'expDate');
		$this->assertEquals('1114', $element);
	}
	
	public function testAccountUpdater8()
	{
		$element = Litle_CreditCard_Model_PaymentLogic::getUpdater($this->domNoNewCard, 'newCardTokenInfo');
		$this->assertTrue($element === NULL);
	}
	
}

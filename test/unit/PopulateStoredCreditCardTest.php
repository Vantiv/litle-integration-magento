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

class PopulateStoredCreditCardTest extends PHPUnit_Framework_TestCase
{
	protected $collections1;
	protected $collections2;
	protected $collections3;
	
	protected function setUp() {
		$this->collections1 = array(
		array('vault_id' => 1, 'order_id' => 1, 'customer_id' => 1, 'last4' => 4242, 'token' => 4234123412124242, 'type' => 'VI', 'bin' => 23422), 
		array('vault_id' => 2, 'order_id' => 2, 'customer_id' => 1, 'last4' => 5555, 'token' => 4234123412125555, 'type' => 'VI', 'bin' => 23424), 
		array('vault_id' => 3, 'order_id' => 3, 'customer_id' => 1, 'last4' => 0001, 'token' => 4234123412120001, 'type' => 'VI', 'bin' => 23423));

		$this->collections2 = array(
		array('vault_id' => 1, 'order_id' => 1, 'customer_id' => 1, 'last4' => 4242, 'token' => 5234123412124242, 'type' => 'VI', 'bin' => 23422),
		array('vault_id' => 2, 'order_id' => 2, 'customer_id' => 1, 'last4' => 4242, 'token' => 4234123412124242, 'type' => 'VI', 'bin' => 23424),
		array('vault_id' => 3, 'order_id' => 3, 'customer_id' => 1, 'last4' => 4242, 'token' => 4234123412124242, 'type' => 'VI', 'bin' => 23423));
		
		$this->collections3 = array(
		array('vault_id' => 1, 'order_id' => 1, 'customer_id' => 1, 'last4' => 4242, 'token' => 4234123412124242, 'type' => 'VI', 'bin' => 23422),
		array('vault_id' => 2, 'order_id' => 2, 'customer_id' => 1, 'last4' => 4242, 'token' => 4234123412125555, 'type' => 'MC', 'bin' => 23424),
		array('vault_id' => 3, 'order_id' => 3, 'customer_id' => 1, 'last4' => 4242, 'token' => 4234123412120001, 'type' => 'AX', 'bin' => 23423));
		
	}
	
	public function test_Basic_Stored_Credit_Cards()
	{		
		$purchases = Litle_CreditCard_Helper_Data::populateStoredCreditCard($this->collections1);
		$this->assertEquals(3, count($purchases));
		$this->assertEquals(4242, $purchases[0]['last4']);
		$this->assertEquals(5555, $purchases[1]['last4']);
		$this->assertEquals(0001, $purchases[2]['last4']);
	}
	
	public function test_Overlapping_Stored_Credit_Cards()
	{
		$purchases = Litle_CreditCard_Helper_Data::populateStoredCreditCard($this->collections2);
		$this->assertEquals(1, count($purchases));
		$this->assertEquals('VI', $purchases[0]['type']);
		$this->assertEquals(NULL, $purchases[1]['type']);
		$this->assertEquals(NULL, $purchases[2]['type']);
	}

	public function test_Overlapping_Stored_Credit_Card_Different_Types()
	{
		$purchases = Litle_CreditCard_Helper_Data::populateStoredCreditCard($this->collections3);
		$this->assertEquals(3, count($purchases));
		$this->assertEquals('VI', $purchases[0]['type']);
		$this->assertEquals('MC', $purchases[1]['type']);
		$this->assertEquals('AX', $purchases[2]['type']);
	}
}
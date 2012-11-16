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
require_once(getenv('MAGENTO_HOME')."/app/code/local/Litle/LitleSDK/Communication.php");

class CommunicationTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider providerAccountNumbers
	 */
	public function testCleanseAccountNumber($input, $expected) {
		$this->assertEquals($expected, Communication::cleanseAccountNumber($input));
	}
	
	public function providerAccountNumbers() {
		return array(
			array('abc','abc'),
			array('',''),
			array('<number>abc</number>','<number>abc</number>'),
			array('<number>123456</number>','<number>XXXX3456</number>'),
		);
	}
	
	/**
	 * @dataProvider providerCardValidationNums
	 */
	public function testCleanseCardValidationNum($input, $expected) {
		$this->assertEquals($expected, Communication::cleanseCardValidationNum($input));
	}
	
	public function providerCardValidationNums() {
		return array(
			array('abc','abc'),
			array('',''),
			array('<cardValidationNum>abc</cardValidationNum>','<cardValidationNum>abc</cardValidationNum>'),
			array('<cardValidationNum>1</cardValidationNum>','<cardValidationNum>NEUTERED</cardValidationNum>'),
			array('<cardValidationNum>12</cardValidationNum>','<cardValidationNum>NEUTERED</cardValidationNum>'),
			array('<cardValidationNum>123</cardValidationNum>','<cardValidationNum>NEUTERED</cardValidationNum>'),
			array('<cardValidationNum>1234</cardValidationNum>','<cardValidationNum>NEUTERED</cardValidationNum>'),
			array('<cardValidationNum>12345</cardValidationNum>','<cardValidationNum>NEUTERED</cardValidationNum>'),
			array('<cardValidationNum>123456</cardValidationNum>','<cardValidationNum>NEUTERED</cardValidationNum>'),
		);
	}
	
	/**
	 * @dataProvider providerPasswords
	 */
	public function testCleansePassword($input, $expected) {
		$this->assertEquals($expected, Communication::cleansePassword($input));
	}
	
	public function providerPasswords() {
		return array(
			array('abc','abc'),
			array('',''),
			array('<password>1</password>','<password>NEUTERED</password>'),
			array('<password>12</password>','<password>NEUTERED</password>'),
			array('<password>123</password>','<password>NEUTERED</password>'),
			array('<password>1234</password>','<password>NEUTERED</password>'),
			array('<password>12345</password>','<password>NEUTERED</password>'),
			array('<password>123456</password>','<password>NEUTERED</password>'),
		);
	}
	
	
	
}

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


class Litle_Editable_Block_Adminhtml_Transaction extends Mage_Adminhtml_Block_Sales_Transactions_Detail {
	public function __construct() {
		parent::__construct();		
	}

	protected function _beforeToHtml() {
		parent::_beforeToHtml();
	}
		
	public function getTxnIdHtml() {
		
		$litle = new Litle_CreditCard_Model_PaymentLogic();
		$url = $litle->getConfigData("url");
		$litleTxnId = $this->_txn->getTxnId();
		$txnType = $this->_txn->getTxnType();
		
		preg_match("/https:\/\/(\w+)\..*/",$url,$matches);
		$base_url = $matches[1];
		
		if($txnType == 'authorization') {
			$litleTxnType = 'authorization';
		}
		else if($txnType == 'capture') {
			$litleTxnType = 'deposit';
		}
		else if($txnType == 'refund') {
			$litleTxnType = 'refund';
		}
		else if($txnType == 'void') {
			if(preg_match("/\d{18}-void/",$litleTxnId) == 0) {
				$litleTxnType = 'authorization/reversal';				
			}
			else {
				return parent::getTxnIdHtml();
			}
		}
		else {
			return parent::getTxnIdHtml();
		}
				
		return "<a href='https://reports.$base_url.litle.com/ui/reports/payments/$litleTxnType/$litleTxnId'>$litleTxnId</a>";
	}

}
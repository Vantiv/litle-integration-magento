<?php
require_once ('Litle/LitleSDK/LitleOnline.php');

class Litle_CreditCard_Model_PaymentLogic extends Mage_Payment_Model_Method_Cc
{

	/**
	 * unique internal payment method identifier
	 */
	protected $_code = 'creditcard';

	protected $_formBlockType = 'creditcard/form_creditCard';

	/**
	 * this should probably be true if you're using this method to take payments
	 */
	protected $_isGateway = true;

	/**
	 * can this method authorise?
	 */
	protected $_canAuthorize = true;

	/**
	 * can this method capture funds?
	 */
	protected $_canCapture = true;

	/**
	 * can we capture only partial amounts?
	 */
	protected $_canCapturePartial = true;

	/**
	 * can this method refund?
	 */
	protected $_canRefund = true;

	protected $_canRefundInvoicePartial = true;

	/**
	 * can this method void transactions?
	 */
	protected $_canVoid = true;

	/**
	 * can admins use this payment method?
	 */
	protected $_canUseInternal = true;

	/**
	 * show this method on the checkout page
	 */
	protected $_canUseCheckout = true;

	/**
	 * available for multi shipping checkouts?
	 */
	protected $_canUseForMultishipping = true;

	/**
	 * can this method save cc info for later use?
	 */
	protected $_canSaveCc = false;
	
	public function getConfigData($fieldToLookFor, $store = null)
	{
		$returnFromThisModel = Mage::getStoreConfig('payment/CreditCard/' . $fieldToLookFor);
		if (is_null($returnFromThisModel)) {
			$returnFromThisModel = parent::getConfigData($fieldToLookFor, $store);
		}

		return $returnFromThisModel;
	}

	public function isFromVT($payment, $txnType)
	{
		$parentTxnId = $payment->getParentTransactionId();
		if ($parentTxnId == 'Litle VT') {
			Mage::throwException(
					"This order was placed using Litle Virtual Terminal. Please process the $txnType by logging into Litle Virtual Terminal (https://reports.litle.com).");
		}
	}

	public function assignData($data)
	{
		if (! ($data instanceof Varien_Object)) {
			$data = new Varien_Object($data);
		}

		if ($this->getConfigData('paypage_enabled')) {
			$info = $this->getInfoInstance();
			$info->setAdditionalInformation('paypage_enabled', $data->getPaypageEnabled());
			$info->setAdditionalInformation('paypage_registration_id', $data->getPaypageRegistrationId());
			$info->setAdditionalInformation('paypage_order_id', $data->getOrderId());
			$info->setAdditionalInformation('cc_vaulted', $data->getCcVaulted());
			$info->setAdditionalInformation('cc_should_save', $data->getCcShouldSave());
		}
        
		if ($this->getConfigData('vault_enable')) {
			$info->setAdditionalInformation('cc_vaulted', $data->getCcVaulted());
			$info->setAdditionalInformation('cc_should_save', $data->getCcShouldSave());
		}

		return parent::assignData($data);
	}

	public function validate()
	{
		// no cc validation required.
		return $this;
	}

	public function litleCcTypeEnum(Varien_Object $payment)
	{
		$typeEnum = '';
		if ($payment->getCcType() == 'AE') {
			$typeEnum = 'AX';
		} elseif ($payment->getCcType() == 'JCB') {
			$typeEnum = 'JC';
		} else {
			$typeEnum = $payment->getCcType();
		}
		return $typeEnum;
	}

	public function getCreditCardInfo(Varien_Object $payment)
	{
		$retArray = array();
		$retArray['type'] = $this->litleCcTypeEnum($payment);
		$retArray['number'] = $payment->getCcNumber();
		preg_match('/\d\d(\d\d)/', $payment->getCcExpYear(), $expYear);
		$retArray['expDate'] = sprintf('%02d%02d', $payment->getCcExpMonth(), $expYear[1]);
		$retArray['cardValidationNum'] = $payment->getCcCid();

		return $retArray;
	}

	/**
	 * Return the last 4 digits of the card number.
	 *
	 * @param Varien_Object $payment
	 * @return string
	 */
	public function getCcLast4($payment)
	{
		$numbersOnly = preg_replace('/[^0-9]*/', '', $payment->getCcNumber());
		return substr($numbersOnly, - 4, 4);
	}

	public function getPaypageInfo($payment)
	{
		$info = $this->getInfoInstance();

		$retArray = array();
		$retArray['type'] = $this->litleCcTypeEnum($payment);
		$retArray['paypageRegistrationId'] = $info->getAdditionalInformation('paypage_registration_id');
		preg_match('/\d\d(\d\d)/', $payment->getCcExpYear(), $expYear);
		$retArray['expDate'] = sprintf('%02d%02d', $payment->getCcExpMonth(), $expYear[1]);
		$retArray['cardValidationNum'] = $payment->getCcCid();

		return $retArray;
	}

	public function getTokenInfo($payment)
	{
		$vaultIndex = $this->getInfoInstance()->getAdditionalInformation('cc_vaulted');
		$vaultCard = Mage::getModel('palorus/vault')->load($vaultIndex);

		$retArray = array();
                $retArray['type'] = $this->litleCcTypeEnum($vaultCard);
		$retArray['litleToken'] = $vaultCard->getToken();
		$retArray['cardValidationNum'] = $payment->getCcCid();
		preg_match('/\d\d(\d\d)/', $vaultCard->getExpirationYear(), $expYear);
	        $retArray['expDate'] = sprintf('%02d%02d', $vaultCard->getExpirationMonth(), $expYear[1]);
		$payment->setCcLast4($vaultCard->getLast4());
		$payment->setCcType($vaultCard->getType());

		return $retArray;
	}

	public function creditCardOrPaypageOrToken($payment, $info)
	{
		$vaultIndex = $info->getAdditionalInformation('cc_vaulted');
		$payment_hash = array();
		if ($vaultIndex > 0) {
			$payment_hash['token'] = $this->getTokenInfo($payment);
		} elseif ($info->getAdditionalInformation('paypage_enabled') == '1') {
			$payment_hash['paypage'] = $this->getPaypageInfo($payment);
		} else {
			$payment_hash['card'] = $this->getCreditCardInfo($payment);
		}
		return $payment_hash;
	}

	public function getContactInformation($contactInfo)
	{
		if (! empty($contactInfo)) {
			$retArray = array();
			$retArray['firstName'] = $contactInfo->getFirstname();
			$retArray['lastName'] = $contactInfo->getLastname();
			$retArray['companyName'] = $contactInfo->getCompany();
			$retArray['addressLine1'] = $contactInfo->getStreet(1);
			$retArray['addressLine2'] = $contactInfo->getStreet(2);
			$retArray['addressLine3'] = $contactInfo->getStreet(3);
			$retArray['city'] = $contactInfo->getCity();
			$retArray['state'] = $contactInfo->getRegion();
			$retArray['zip'] = $contactInfo->getPostcode();
			$retArray['country'] = $contactInfo->getCountry();
			$retArray['email'] = $contactInfo->getCustomerEmail();
			$retArray['phone'] = $contactInfo->getTelephone();
			return $retArray;
		}
		return null;
	}

	public function getBillToAddress(Varien_Object $payment)
	{
		$order = $payment->getOrder();
		if (! empty($order)) {
			$billing = $order->getBillingAddress();
			if (! empty($billing)) {
				return $this->getContactInformation($billing);
			}
		}
		return null;
	}

	public function getShipToAddress(Varien_Object $payment)
	{
		$order = $payment->getOrder();
		if (! empty($order)) {
			$shipping = $order->getShippingAddress();
			if (! empty($shipping)) {
				return $this->getContactInformation($shipping);
			}
		}
		return null;
	}

	public function getIpAddress(Varien_Object $payment)
	{
		$order = $payment->getOrder();
		if (! empty($order)) {
			return $order->getRemoteIp();
		}
		return null;
	}

	public function getMerchantId(Varien_Object $payment)
	{
		$order = $payment->getOrder();
		$currency = $order->getOrderCurrencyCode();
		$string2Eval = 'return array' . $this->getConfigData('merchant_id') . ';';
		$merchant_map = eval($string2Eval);
		$merchantId = $merchant_map[$currency];
		return $merchantId;
	}

	public function merchantData(Varien_Object $payment)
	{
		$order = $payment->getOrder();
		$hash = array(
				'user' => $this->getConfigData('user'),
				'password' => $this->getConfigData('password'),
				'merchantId' => $this->getMerchantId($payment),
				'merchantSdk' => 'Magento;8.15.0',
				'reportGroup' => $this->getMerchantId($payment),
				'customerId' => $order->getCustomerEmail(),
				'url' => $this->getConfigData('url'),
				'proxy' => $this->getConfigData('proxy'),
				'timeout' => $this->getConfigData('timeout'),
				'batch_requests_path' => 'MAGENTO', //Magento doesn't use batch
				'sftp_username' => 'MAGENTO', //Magento doesn't use batch
				'sftp_password' => 'MAGENTO', //Magento doesn't use batch
				'batch_url' => 'MAGENTO', //Magento doesn't use batch
				'tcp_port' => 'MAGENTO', //Magento doesn't use batch
				'tcp_ssl' => 'MAGENTO', //Magento doesn't use batch
				'tcp_timeout' => 'MAGENTO', //Magento doesn't use batch
				'litle_requests_path' => 'MAGENTO', //Magento doesn't use batch
				'print_xml' => 'false' //Magento uses debug_enabled instead
		);
		return $hash;
	}

	public function getCustomBilling($url)
	{
		$retArray = array();

		if (strlen($url) > 13) {
			$url = str_replace('http://', '', $url);
			$url = str_replace('https://', '', $url);
			$url_temp = explode('/', $url);
			$url = $url_temp['0'];
			if (strlen($url) > 13) {
				$url = str_replace('www.', '', $url);
				if (strlen($url) > 13) {
					$url_temp2 = explode('.', $url);
					$count = count($url_temp2);
				}
				if ($count == 2) {
					if (strlen($url_temp2['0'] . '.' . $url_temp2['1']) > 13) {
						$url = $url_temp2['0'];
					} else {
						$url = $url_temp2['0'] . '.' . $url_temp2['1'];
					}
				}
				if($count == 1) {
				    $url = substr($url_temp2['0'], 0, 13);
				}
			}
		}

		$url = substr($url, 0, 13);
		if (substr($url, 12) === '.') {
			$url = substr($url, 0, 12);
		} elseif (substr($url, 0) === '.') {
			$url = substr($url, 1, 12);
		}
		$retArray['url'] = $url;

		return $retArray;
	}

	public function getOrderDate(Varien_Object $payment)
	{
		$order = $payment->getOrder();
		$date = $order->getCreatedAtStoreDate();
		return Mage::getModel('core/date')->date('Y-m-d',$date);
	}

	public function getLineItemData(Varien_Object $payment)
	{
		$order = $payment->getOrder();
		$items = $order->getAllItems();
		$i = 0;
		$lineItemArray = array();
		foreach ($items as $itemId => $item) {
			$name = $item->getName();
			$unitPrice = $item->getPrice();
			$sku = $item->getSku();
			$ids = $item->getProductId();
			$qty = $item->getQtyToInvoice();

			if (strlen($name) > 26)
				$name = substr($name, 0, 26);

			$lineItemArray[$i] = array(
					'itemSequenceNumber' => ($i + 1),
					'itemDescription' => $name,
					'productCode' => $ids,
					'quantity' => $qty,
					'lineItemTotal' => Mage::helper('creditcard')->formatAmount(($unitPrice * $qty), true),
					'unitCost' => Mage::helper('creditcard')->formatAmount(($unitPrice), true)
			);
			$i ++;
		}
		return $lineItemArray;
	}

	public function getEnhancedData(Varien_Object $payment)
	{
		$order = $payment->getOrder();
		$billing = $order->getBillingAddress();

		$hash = array(
				'salesTax' => Mage::helper('creditcard')->formatAmount($order->getTaxAmount(), true),
				'discountAmount' => Mage::helper('creditcard')->formatAmount($order->getDiscountAmount(), true),
				'shippingAmount' => Mage::helper('creditcard')->formatAmount($order->getShippingAmount(), true),
				'destinationPostalCode' => $billing->getPostcode(),
				'destinationCountryCode' => $billing->getCountry(),
				'orderDate' => $this->getOrderDate($payment),
				'detailTax' => array(
						array(
								'taxAmount' => Mage::helper('creditcard')->formatAmount($order->getTaxAmount(), true)
						)
				),
				'lineItemData' => $this->getLineItemData($payment)
		);
		return $hash;
	}

	public function getFraudCheck(Varien_Object $payment)
	{
		$order = $payment->getOrder();
		$ip = $order->getRemoteIp();
		$ipv4Regex = "/\A(?:\d{1,3}\.){3}\d{1,3}\z/";
		$matches = preg_match($ipv4Regex, $ip);
		if($matches === 1) {
    		$hash = array(
	   			'customerIpAddress' => $ip
		  );
		}
		else {
		    Mage::log("Not sending ip address " . $ip . " because it isn't ipv4", null, "litle.log");
		    $hash = array();
		}
		return $hash;
	}

	/**
	 * Parses Litle response to obtain update customer information.
	 *
	 * @param DOMDocument $litleResponse
	 * @param string $parentNode
	 * @param string $childNode
	 * @return DOMNode
	 */
	public function getUpdater($litleResponse, $parentNode, $childNode = null)
	{
		if (is_null($childNode)) {
			$new = $litleResponse->getElementsByTagName($parentNode)->item(0);
		} else {
			$new = $litleResponse->getElementsByTagName($parentNode)
				->item(0)
				->getElementsByTagName($childNode)
				->item(0)->nodeValue;
		}

		return $new;
	}

	/**
	 * Updates customer account information with most current from Litle.
	 *
	 * @param Varien_Object $payment
	 * @param DOMDocument $litleResponse
	 */
	public function accountUpdater(Varien_Object $payment, $litleResponse)
	{
		if ($this->getUpdater($litleResponse, 'newCardInfo') !== null) {
			$payment->setCcLast4(
					substr($this->getUpdater($litleResponse, 'newCardInfo', 'number'), - 4));
			$payment->setCcType($this->getUpdater($litleResponse, 'newCardInfo', 'type'));
			$payment->setCcExpDate($this->getUpdater($litleResponse, 'newCardInfo', 'expDate'));
		} elseif ($this->getUpdater($litleResponse, 'newCardTokenInfo') !== null) {
			$payment->setCcNumber($this->getUpdater($litleResponse, 'newCardTokenInfo', 'litleToken'));
			$payment->setCcLast4(
					substr($this->getUpdater($litleResponse, 'newCardTokenInfo', 'litleToken'), - 4));
			$payment->setCcType($this->getUpdater($litleResponse, 'newCardTokenInfo', 'type'));
			$payment->setCcExpDate($this->getUpdater($litleResponse, 'newCardTokenInfo', 'expDate'));
		}
	}

	/**
	 * Update Vaulted card information.
	 *
	 * @param Varien_Object $payment
	 * @param DOMDocument $litleResponse
	 */
	protected function _saveToken(Varien_Object $payment, DOMDocument $litleResponse)
	{
		if (!is_null($this->getUpdater($litleResponse, 'tokenResponse')) &&
			!is_null($this->getUpdater($litleResponse, 'tokenResponse', 'litleToken'))) {

			$vault = Mage::getModel('palorus/vault')->setTokenFromPayment(
					$payment,
					$this->getUpdater($litleResponse, 'tokenResponse', 'litleToken'),
					$this->getUpdater($litleResponse, 'tokenResponse', 'bin'));

			if ($vault) {
				$this->getInfoInstance()->setAdditionalInformation('vault_id', $vault->getId());
			}
		}
	}

	/**
	 *
	 * @param Varien_Object $payment
	 * @param DOMDocument $litleResponse
	 * @throws Mage_Payment_Model_Info_Exception
	 * @return boolean
	 */
	public function processResponse(Varien_Object $payment, $litleResponse)
	{
		$this->accountUpdater($payment, $litleResponse);

		$message = XmlParser::getAttribute($litleResponse, 'litleOnlineResponse', 'message');
		if ($message == 'Valid Format') {
			$isSale = ($payment->getCcTransId() != null) ? false : true;
			if (isset($litleResponse)) {
				$litleResponseCode = XMLParser::getNode($litleResponse, 'response');
				if ($litleResponseCode != '000') {
					if ($isSale) {
						if(Mage::getStoreConfig('payment/CreditCard/debug_enable')) {
							Mage::log("Had an unsuccessful response in an authorization/sale - response code: " . $litleResponseCode, null, "litle.log");
						}
						$customerId = $payment->getOrder()->getCustomerId();
						$orderId = $payment->getOrder()->getId();
						$this->writeFailedTransactionToDatabase($customerId, null, $message, $litleResponse); //null order id because the order hasn't been created yet
						
						Mage::throwException("The order was not approved.  Please try again later or contact us.  For your reference, the transaction id is " . XMLParser::getNode($litleResponse, 'litleTxnId'));
						
					}		
					else {
						$this->handleResponseForNonSuccessfulBackendTransactions($payment, $litleResponse, $litleResponseCode);
					}
				} else {
					$payment->setStatus('Approved')
						->setCcTransId(XMLParser::getNode($litleResponse, 'litleTxnId'))
						->setLastTransId(XMLParser::getNode($litleResponse, 'litleTxnId'))
						->setTransactionId(XMLParser::getNode($litleResponse, 'litleTxnId'))
						->setIsTransactionClosed(0)
						->setTransactionAdditionalInfo('additional_information',
							XMLParser::getNode($litleResponse, 'message'));
				}
				return true;
			}
		} else {
			Mage::throwException($message);
		}
	}
	
	public function writeFailedTransactionToDatabase($customerId, $orderId, $message, $xmlDocument, $txnType) {
		$orderNumber = 0;
		if($orderId === null) {
			$orderId = 0;
		}
		else {
			$order = Mage::getModel("sales/order")->load($orderId);
			$orderNumber = $order->getData("increment_id");
		}
		if($customerId === null) {
			$customerId = 0;
		}
		$config = Mage::getResourceModel("sales/order")->getReadConnection()->getConfig();
		$host = $config['host'];
		$username = $config['username'];
		$password = $config['password'];
		$dbname = $config['dbname'];

		$con = mysql_connect($host,$username,$password);
		$fullXml = $xmlDocument->saveXML();
		if (!$con)
		{
			Mage::log("Failed to write failed transaction to database.  Transaction details: " . $fullXml, null, "litle_failed_transactions.log");
		}
		else {
			$selectedDb = mysql_select_db($dbname, $con);
			if(!$selectedDb) {
				Mage::log("Can't use selected database " . $dbname, null, "litle.log");
			}
			$fullXml = mysql_real_escape_string($fullXml);
			$litleTxnId = XMLParser::getNode($xmlDocument, 'litleTxnId');
			$sql = "insert into litle_failed_transactions (customer_id, order_id, message, full_xml, litle_txn_id, active, transaction_timestamp, order_num) values (" . $customerId . ", " . $orderId . ", '" . $message . "', '" . $fullXml . "', '" . $litleTxnId . "', true, now()," . $orderNumber . ")";
			$result = mysql_query($sql);
			if(!$result) {
				Mage::log("Insert failed with error message: " . mysql_error(), null, "litle.log");
				Mage::log("Query executed: " . $sql, null, "litle.log");
			}
			$sql = "select * from sales_payment_transaction where order_id = " . $orderId . " order by created_at asc";
			$result = mysql_query($sql);
			Mage::log("Executed sql: " . $sql, null, "litle.log");
			Mage::log($result, null, "litle.log");
			while($row = mysql_fetch_assoc($result)) {
				Mage::log($row['transaction_id'], null, "litle.log");
				$sql = "insert into sales_payment_transaction (parent_id, order_id, payment_id, txn_id, parent_txn_id, txn_type, is_closed, created_at, additional_information) values (".$row['transaction_id'].", ".$orderId.", ".$orderId.", ".$litleTxnId.", ".$row['txn_id'].", '".$txnType."', 0,now(),'".serialize(array('message'=>message))."')";
			}
			Mage::log("Sql to execute is: " . $sql, null, "litle.log");
			$result = mysql_query($sql);
			if(!$result) {
				Mage::log("Insert failed with error message: " . mysql_error(), null, "litle.log");
				Mage::log("Query executed: " . $sql, null, "litle.log");
			}
			
			$sql = "insert into sales_flat_order_status_history (parent_id, is_customer_notified, is_visible_on_front, comment, status, created_at, entity_name) values (".$orderId.", 2, 0,'".$message.". Transaction ID: ".$litleTxnId."','processing',now(),'".$txnType."')";
			Mage::log("Sql to execute is: " . $sql, null, "litle.log");
			$result = mysql_query($sql);
			if(!$result) {
				Mage::log("Insert failed with error message: " . mysql_error(), null, "litle.log");
				Mage::log("Query executed: " . $sql, null, "litle.log");
			}
				
			mysql_close($con);
		}
	}
	
	public function handleResponseForNonSuccessfulBackendTransactions(Varien_Object $payment, $litleResponse, $litleResponseCode) {
		$order = $payment->getOrder();
		$litleMessage = XMLParser::getNode($litleResponse, 'message');
		$litleTxnId = XMLParser::getNode($litleResponse, 'litleTxnId');
		$customerId = $order->getData('customer_id');
		$orderId = $order->getId();
		
		if($litleResponseCode === '306') {
		
			$this->setOrderStatusAndCommentsForFailedTransaction(	$payment, $litleTxnId,
															Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID, 
															Mage_Sales_Model_Order::STATE_CANCELED,
															Mage_Payment_Model_Method_Abstract::STATUS_VOID, 
															"Authorization has expired - no need to reverse.  The original Authorization is no longer valid, because it has expired.  You can not perform an Authorization Reversal for an expired Authorization.",
															true);
		}
		elseif($litleResponseCode === '311') {
			$this->setOrderStatusAndCommentsForFailedTransaction(	$payment, $litleTxnId, 
															Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND, 
															Mage_Sales_Model_Order::STATE_COMPLETE,
															Mage_Payment_Model_Method_Abstract::STATUS_APPROVED, 
															"Deposit is already referenced by a chargeback.  The deposit is already referenced by a chargeback; therefore, a refund cannot be processed against the original transaction.",
															true);
		}
		elseif($litleResponseCode === '316') {
			$this->setOrderStatusAndCommentsForFailedTransaction(	$payment, $litleTxnId,
															Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND, 
															Mage_Sales_Model_Order::STATE_COMPLETE,
															Mage_Payment_Model_Method_Abstract::STATUS_APPROVED, 
															"Automatic refund already issued.  This refund transaction is a duplicate for one already processed automatically by the Litle Fraud Chargeback Prevention Service.",
															true);
		}
		elseif($litleResponseCode === '335') {
			$descriptiveMessage = "This method of payment does not support authorization reversals.  You can not perform an Authorization Reversal transaction for this payment type."; 
			$this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID);
		}
		elseif($litleResponseCode === '336') {
			$descriptiveMessage = "Reversal amount does not match Authorization amount.  For a merchant initiated reversal against an American Express authorization, the reversal amount must match the authorization amount exactly."; 
			$this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID);
		}
		elseif($litleResponseCode === '361') {
			$descriptiveMessage = "The order #".$order->getIncrementId()." can not be captured.  Authorization no longer available.  The authorization for this transaction is no longer available;  the authorization has already been consumed by another capture."; 
			$this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE);
		}
		elseif($litleResponseCode === '362') {
			$descriptiveMessage = "Transaction Not Voided - Already Settled.  This transaction cannot be voided; it has already been delivered to the card networks.  You may want to try a refund instead."; 
			$this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID);
		}
		elseif($litleResponseCode === '365') {
			$descriptiveMessage = "Total credit amount exceeds capture amouont.  The amount of the credit is greater than the capture, or the amount of this credit plus other credits already referencing this capture are greater than the capture amount."; 
			$this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND);
		}
		elseif($litleResponseCode === '370') {
			$descriptiveMessage = "Internal System Error - Call Litle.  There is a problem with the Litle System.  Contact support@litle.com and provide the following transaction id: " . $litleTxnId; 
			$this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
		}
		else { 			
			$descriptiveMessage = "Transaction was not approved and Litle's Magento extension can not tell why. Contact Litle at support@litle.com and provide the following transaction id: " . $litleTxnId; 
			$this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
		}
	
	}

	public function setOrderStatusAndCommentsForFailedTransaction($payment, $litleTxnId, $transactionType, $orderState, $paymentStatus, $litleMessage, $closed) {
		$paymentHelp = new Litle_CreditCard_Model_Lpayment();
		$paymentHelp->setOrder($payment->getOrder());
		$transaction = $paymentHelp->addTransaction(transactionType, null, true, $litleMessage);
		$payment->setStatus($paymentStatus)
			->setCcTransId($litleTxnId)
			->setLastTransId($litleTxnId)
			->setTransactionId($litleTxnId)
			->setIsTransactionClosed($closed)
			->setTransactionAdditionalInfo('additional_information', $litleMessage);	
	}
	
	public function showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $messageToShow, $litleTxnId, $txnType) {
		$this->writeFailedTransactionToDatabase($customerId, $orderId, $litleMessage, $litleResponse, $txnType);
		$resource = Mage::getSingleton('core/resource');
		$conn = $resource->getConnection('core_read');
		$query = 'select failed_transactions_id from litle_failed_transactions where litle_txn_id = ' . $litleTxnId;
		$failedTransactionId = $conn->fetchOne($query);
		$url = Mage::getUrl('palorus/adminhtml_myform/failedtransactionsview/') . 'failed_transactions_id/' . $failedTransactionId;
		Mage::throwException($messageToShow . "For your reference, the transaction id is <a href='" . $url . "'>".$litleTxnId."</a>");
	}

	/**
	 * this method is called if we are just authorising a transaction
	 */
	public function authorize(Varien_Object $payment, $amount)
	{
	    // What about this?   Mage::app()->getStore()->isAdmin()
		// @TODO This is the wrong way to do this.
		if (preg_match('/sales_order_create/i', $_SERVER['REQUEST_URI']) &&
				 ($this->getConfigData('paypage_enable') == '1')) {
			$payment->setStatus('N/A')
				->setCcTransId('Litle VT')
				->setLastTransId('Litle VT')
				->setTransactionId('Litle VT')
				->setIsTransactionClosed(0)
				->setCcType('Litle VT');
		} else {
			$order = $payment->getOrder();
			$orderId = $order->getIncrementId();
			$amountToPass = Mage::helper('creditcard')->formatAmount($amount, true);

			if (! empty($order)) {
				$info = $this->getInfoInstance();
				if (!$info->getAdditionalInformation('orderSource')) {
					$info->setAdditionalInformation('orderSource', 'ecommerce');
				}

                $hash_in = $this->generateAuthorizationHash($orderId, $amountToPass, $info, $payment);

				$litleRequest = new LitleOnlineRequest();
				$litleResponse = $litleRequest->authorizationRequest($hash_in);
				$this->processResponse($payment, $litleResponse);

				Mage::helper('palorus')->saveCustomerInsight($payment, $litleResponse);
				if (!is_null($info->getAdditionalInformation('cc_should_save'))) {
					$this->_saveToken($payment, $litleResponse);
				}
			}
		}

		return $this;
	}
	
	function generateAuthorizationHash($orderId, $amountToPass, $info, $payment) {
        $hash = array(
                'orderId' => $orderId,
                'id' => $orderId,
                'amount' => $amountToPass,
                'orderSource' => $info->getAdditionalInformation('orderSource'),
                'billToAddress' => $this->getBillToAddress($payment),
                'shipToAddress' => $this->getAddressInfo($payment),
                'cardholderAuthentication' => $this->getFraudCheck($payment),
                'enhancedData' => $this->getEnhancedData($payment),
                'customBilling' => $this->getCustomBilling(
                        Mage::app()->getStore()
                            ->getBaseUrl())
        );
        
        $payment_hash = $this->creditCardOrPaypageOrToken($payment, $info);
        $hash_temp = array_merge($hash, $payment_hash);
        $merchantData = $this->merchantData($payment);
        $hash_in = array_merge($hash_temp, $merchantData);
        
        return $hash_in;
	}

	/**
	 * this method is called if we are authorising AND capturing a transaction
	 */
	public function capture(Varien_Object $payment, $amount)
	{
		if (preg_match('/sales_order_create/i', $_SERVER['REQUEST_URI']) &&
				 ($this->getConfigData('paypage_enable') == '1')) {
			$payment->setStatus('N/A')
				->setCcTransId('Litle VT')
				->setLastTransId('Litle VT')
				->setTransactionId('Litle VT')
				->setIsTransactionClosed(0)
				->setCcType('Litle VT');

			return;
		}

		$this->isFromVT($payment, 'capture');

		$order = $payment->getOrder();
		if (! empty($order)) {
			$info = $this->getInfoInstance();
			if (!$info->getAdditionalInformation('orderSource')) {
				$info->setAdditionalInformation('orderSource', 'ecommerce');
			}

			$orderId = $order->getIncrementId();
			$amountToPass = Mage::helper('creditcard')->formatAmount($amount, true);
			$isPartialCapture = ($amount < $order->getGrandTotal()) ? 'true' : 'false';
			$isSale = ($payment->getCcTransId() != null) ? false : true;

			if (! $isSale) {
				$hash = array(
						'litleTxnId' => $payment->getParentTransactionId(),
						'amount' => $amountToPass,
						'partial' => $isPartialCapture
				);
			} else {
				$hash_temp = array(
						'orderId' => $orderId,
						'amount' => $amountToPass,
						'orderSource' => $info->getAdditionalInformation('orderSource'),
						'billToAddress' => $this->getBillToAddress($payment),
						'shipToAddress' => $this->getAddressInfo($payment),
						'enhancedData' => $this->getEnhancedData($payment)
				);
				$payment_hash = $this->creditCardOrPaypageOrToken($payment, $info);
				$hash = array_merge($hash_temp, $payment_hash);
			}
			$merchantData = $this->merchantData($payment);
			$hash_in = array_merge($hash, $merchantData);
			$litleRequest = new LitleOnlineRequest();

			if ($isSale) {
				$litleResponse = $litleRequest->saleRequest($hash_in);
				Mage::helper('palorus')->saveCustomerInsight($payment, $litleResponse);
			} else {
				$litleResponse = $litleRequest->captureRequest($hash_in);
			}
            
			if (! is_null($info->getAdditionalInformation('cc_should_save'))) {
				$this->_saveToken($payment, $litleResponse);
			}
		}
		$this->processResponse($payment, $litleResponse);

		return $this;
	}

	/**
	 * called if refunding
	 */
	public function refund(Varien_Object $payment, $amount)
	{
		$this->isFromVT($payment, 'refund');

		$order = $payment->getOrder();
		$isPartialRefund = ($amount < $order->getGrandTotal()) ? true : false;

		$amountToPass = Mage::helper('creditcard')->formatAmount($amount, true);
		if (! empty($order)) {
			$hash = array(
					'litleTxnId' => $this->findCaptureLitleTxnToRefundForPayment($payment),
					'amount' => $amountToPass
			);
			$merchantData = $this->merchantData($payment);
			$hash_in = array_merge($hash, $merchantData);
			$litleRequest = new LitleOnlineRequest();
			$litleResponse = $litleRequest->creditRequest($hash_in);
		}
		$this->processResponse($payment, $litleResponse);

		return $this;
	}
	
	function findCaptureLitleTxnToRefundForPayment($payment) {
	   $capture = $payment->lookupTransaction(false, Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE);
	   return $capture->getTxnId();
	}

	/**
	 * called if voiding a payment
	 */
	public function void(Varien_Object $payment)
	{
		$this->isFromVT($payment, 'void');

		$order = $payment->getOrder();
		if (! empty($order)) {
			$hash = array(
					'litleTxnId' => $payment->getCcTransId()
			);
			$merchantData = $this->merchantData($payment);
			$hash_in = array_merge($hash, $merchantData);
			$litleRequest = new LitleOnlineRequest();

			if (Mage::helper('creditcard')->isStateOfOrderEqualTo($order,
					Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH)) {
				$litleResponse = $litleRequest->authReversalRequest($hash_in);
			} else {
				$litleResponse = $litleRequest->voidRequest($hash_in);
			}
		}
		$this->processResponse($payment, $litleResponse);

		return $this;
	}

	public function cancel(Varien_Object $payment)
	{
		$this->void($payment);


		return $this;
	}
}

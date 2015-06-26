<?php
require_once('Litle/LitleSDK/LitleOnline.php');

class Litle_LEcheck_Model_PaymentLogic extends Mage_Payment_Model_Method_Abstract
{
	/**
	 * unique internal payment method identifier
	 */
	protected $_code = 'lecheck';

	protected $_formBlockType = 'lecheck/form_lEcheck';

	/**
	 * this should probably be true if you're using this
	 * method to take payments
	 */
	protected $_isGateway               = true;

	/**
	 * can this method authorise?
	 */
	protected $_canAuthorize            = true;

	/**
	 * can this method capture funds?
	 */
	protected $_canCapture              = true;

	/**
	 * can we capture only partial amounts?
	 */
	protected $_canCapturePartial       = true;

	/**
	 * can this method refund?
	 */
	protected $_canRefund               = true;

	protected $_canRefundInvoicePartial       = true;

	/**
	 * can this method void transactions?
	 */
	protected $_canVoid                 = true;

	/**
	 * can admins use this payment method?
	 */
	protected $_canUseInternal          = true;

	/**
	 * show this method on the checkout page
	 */
	protected $_canUseCheckout          = true;

	/**
	 * available for multi shipping checkouts?
	 */
	protected $_canUseForMultishipping  = true;

	/**
	 * can this method save cc info for later use?
	 */
	protected $_canSaveCc = false;

	public function assignData($data)
	{
		if (!($data instanceof Varien_Object)) {
			$data = new Varien_Object($data);
		}

		$info = $this->getInfoInstance();
		$info->setAdditionalInformation('echeck_routing_num', $data->getEcheckRoutingNumber());
		$info->setAdditionalInformation('echeck_bank_acc_num', $data->getEcheckBankAcctNum());
		$info->setAdditionalInformation('echeck_acc_type', $data->getEcheckAccountType());
		$info->setAdditionalInformation('echeck_company_name', $data->getEcheckCompanyName());

		return $this;
	}

	public function getConfigData($fieldToLookFor, $store = NULL)
	{
		$returnFromThisModel = "";
		
		if( $fieldToLookFor == "title" || $fieldToLookFor == "active" || $fieldToLookFor == "accounttypes"
			|| $fieldToLookFor == "payment_action" || $fieldToLookFor == "order_status"
			|| $fieldToLookFor == "companyname_enable"){
			if ($fieldToLookFor == "companyname_enable") {
				Mage::log("hi", null, "lecheck_fe.log");				
			}
			$returnFromThisModel = Mage::getStoreConfig('payment/LEcheck/' . $fieldToLookFor);
		}
		else{
			$returnFromThisModel = Mage::getStoreConfig('payment/CreditCard/' . $fieldToLookFor);
		}

		if( $returnFromThisModel == NULL ) {
			$returnFromThisModel = parent::getConfigData($fieldToLookFor, $store);
			}
		return $returnFromThisModel;
	}

	public function getEcheckInfo(Varien_Object $payment)
	{
		$info = $this->getInfoInstance();
		$retArray = array();
		$retArray["accNum"] = $info->getAdditionalInformation('echeck_bank_acc_num');
		$retArray["accType"] = $info->getAdditionalInformation('echeck_acc_type');
		$retArray["routingNum"] = $info->getAdditionalInformation('echeck_routing_num');
		return $retArray;
	}

	public function getContactInformation($contactInfo)
	{
		$retArray = array();
		if(!empty($contactInfo)){
			$retArray["firstName"] =$contactInfo->getFirstname();
			$retArray["lastName"] = $contactInfo->getLastname();
			$retArray["companyName"] = $contactInfo->getCompany();
			$retArray["addressLine1"] = $contactInfo->getStreet(1);
			$retArray["addressLine2"] = $contactInfo->getStreet(2);
			$retArray["addressLine3"] = $contactInfo->getStreet(3);
			$retArray["city"] = $contactInfo->getCity();
			$region = Mage::getModel('directory/region')->load($contactInfo->getRegionId());
			$retArray["state"] = $region->getCode();
			$retArray["zip"] = $contactInfo->getPostcode();
			$retArray["country"] = $contactInfo->getCountry();
			$retArray["email"] = $contactInfo->getCustomerEmail();
			$retArray["phone"] = $contactInfo->getTelephone();
		}

		// get company name from onepage
		$info = $this->getInfoInstance();
		$company = $info->getAdditionalInformation('echeck_company_name');
		if ($company) {
			$retArray["companyName"] = $company;
			$contactInfo->setCompany($company)->save();
		}
		
		return (count($retArray) > 0) ? $retArray : NULL;
	}


	public function getBillToAddress(Varien_Object $payment)
	{
		$order = $payment->getOrder();
		if(!empty($order)){
			$billing = $order ->getBillingAddress();
			if(!empty($billing)){
				return $this->getContactInformation($billing);
			}
		}
		return NULL;
	}

	public function getShipToAddress(Varien_Object $payment)
	{
		$order = $payment->getOrder();
		if(!empty($order)){
			$shipping = $order->getShippingAddress();
			if(!empty($shipping)){
				return $this->getContactInformation($shipping);
			}
		}
		return NULL;
	}
	
	public function getMerchantId(Varien_Object $payment){
		$order = $payment->getOrder();
		$currency = $order->getOrderCurrencyCode();
		$string2Eval = 'return array' . $this->getConfigData("merchant_id") . ';';
		$merchant_map = eval($string2Eval);
		$merchantId = $merchant_map[$currency];
		return $merchantId;
	}
	

	public function merchantData(Varien_Object $payment)
	{
        $order = $payment->getOrder();
        $version = Mage::getModel('core_resource/resource')->getDbVersion($this->getCode() . '_setup');
        $hash = array('user'=> $this->getConfigData("user"),
 					'password'=> $this->getConfigData("password"),
					'merchantId'=>$this->getMerchantId($payment),
	                'merchantSdk' => 'Magento;' . $version,
					'reportGroup'=>$this->getMerchantId($payment),
                	'customerId' => $order->getCustomerEmail(),
					'url'=>$this->getConfigData("url"),	
					'proxy'=>$this->getConfigData("proxy"),
					'timeout'=>$this->getConfigData("timeout"),
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

	public function processResponse(Varien_Object $payment,$litleResponse){
		$message = XmlParser::getAttribute($litleResponse,'litleOnlineResponse','message');
		if ($message == "Valid Format"){
			if( isset($litleResponse))
			{
				$litleResponseCode = XMLParser::getNode($litleResponse,'response');
				if($litleResponseCode != "000")
				{
				if(($litleResponseCode === "362") && Mage::helper("creditcard")->isStateOfOrderEqualTo($payment->getOrder(), Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE))
					{
						Mage::throwException("The void did not go through.  Do a refund instead.");
					}
					else
					{
						$payment
						->setStatus("Rejected")
						->setCcTransId(XMLParser::getNode($litleResponse,'litleTxnId'))
						->setLastTransId(XMLParser::getNode($litleResponse,'litleTxnId'))
						->setTransactionId(XMLParser::getNode($litleResponse,'litleTxnId'))
						->setIsTransactionClosed(0)
						->setTransactionAdditionalInfo("additional_information", XMLParser::getNode($litleResponse,'message'));
							
						throw new Mage_Payment_Model_Info_Exception(Mage::helper('core')->__("Transaction was not approved. Contact us or try again later."));
					}
				}
				else
				{
					$payment
					->setStatus("Approved")
					->setCcTransId(XMLParser::getNode($litleResponse,'litleTxnId'))
					->setLastTransId(XMLParser::getNode($litleResponse,'litleTxnId'))
					->setTransactionId(XMLParser::getNode($litleResponse,'litleTxnId'))
					->setIsTransactionClosed(0)
					->setTransactionAdditionalInfo("additional_information", XMLParser::getNode($litleResponse,'message'));
				}
				return $this;
			}
		}
		else{
			Mage::throwException($message);
		}
	}
	/**
	 * this method is called if we are just authorising
	 * a transaction
	 */
	public function authorize(Varien_Object $payment, $amount)
	{
		$order = $payment->getOrder();
		$orderId = $order->getIncrementId();
		$amountToPass = ($amount* 100);

		if (!empty($order)){
			$hash = array(
	 					'orderId'=> $orderId,
	 					'id'=> $orderId,
	 					'amount'=> $amountToPass,
	 					'orderSource'=> "ecommerce",
						'verify'=>'true',
						'billToAddress'=> $this->getBillToAddress($payment),
						'shipToAddress'=> $this->getAddressInfo($payment),
	 					'echeck'=> $this->getEcheckInfo($payment)
			);
			$merchantData = $this->merchantData($payment);
			$hash_in = array_merge($hash,$merchantData);
			$litleRequest = new LitleOnlineRequest();
			$litleResponse = $litleRequest->echeckVerificationRequest($hash_in);
			$this->processResponse($payment,$litleResponse);
		}
	}

	/**
	 * this method is called if we are authorising AND
	 * capturing a transaction
	 */
	public function capture (Varien_Object $payment, $amount)
	{
		$order = $payment->getOrder();
		$orderId =$order->getIncrementId();
		$amountToPass = ($amount* 100);
		if (!empty($order)){
			$hash = array(
	 					'orderId'=> $orderId,
	 					'id'=> $orderId,
	 					'amount'=> $amountToPass,
	 					'orderSource'=> "ecommerce",
						'billToAddress'=> $this->getBillToAddress($payment),
						'shipToAddress'=> $this->getAddressInfo($payment),
	 					'echeck'=> $this->getEcheckInfo($payment)
			);
			$merchantData = $this->merchantData($payment);
			$hash_in = array_merge($hash,$merchantData);
			$litleRequest = new LitleOnlineRequest();
			$litleResponse = $litleRequest->echeckSaleRequest($hash_in);
		}
		$this->processResponse($payment,$litleResponse);
	}
	/**
	* this method is called if we are authorising AND
	* capturing a transaction with verification
	*/
	public function order (Varien_Object $payment, $amount)
	{
		$order = $payment->getOrder();
		$orderId =$order->getIncrementId();
		$amountToPass = ($amount* 100);
		if (!empty($order)){
			$hash = array(
				'orderId'=> $orderId,
				'amount'=> $amountToPass,
				'orderSource'=> "ecommerce",
				'verify'=>'true',
				'billToAddress'=> $this->getBillToAddress($payment),
				'shipToAddress'=> $this->getAddressInfo($payment),
				'echeck'=> $this->getEcheckInfo($payment)
			);
			$merchantData = $this->merchantData($payment);
			$hash_in = array_merge($hash,$merchantData);
			$litleRequest = new LitleOnlineRequest();
			$litleResponse = $litleRequest->echeckSaleRequest($hash_in);
		}
		$this->processResponse($payment,$litleResponse);
	}

	/**
	 * called if refunding
	 */
	public function refund (Varien_Object $payment, $amount)
	{
		$order = $payment->getOrder();
		$isPartialRefund = ($amount < $order->getGrandTotal()) ? true : false;
		
			$amountToPass = ($amount* 100);
			if (!empty($order)){
				$hash = array(
						'litleTxnId' => $payment->getCcTransId(),
						'amount' => $amountToPass
				);
			
				$merchantData = $this->merchantData($payment);
				$hash_in = array_merge($hash,$merchantData);
				$litleRequest = new LitleOnlineRequest();
				$litleResponse = $litleRequest->echeckCreditRequest($hash_in);
			}
			
			$this->processResponse($payment,$litleResponse);
		return $this;
	}

	/**
	 * called if voiding a payment
	 */
	public function void (Varien_Object $payment)
	{
		$order = $payment->getOrder();
		if (!empty($order)){
			$hash = array(
						'litleTxnId' => $payment->getCcTransId()
			);
			$merchantData = $this->merchantData($payment);
			$hash_in = array_merge($hash,$merchantData);
			$litleRequest = new LitleOnlineRequest();
			$litleResponse = $litleRequest->echeckVoidRequest($hash_in);
		}
		$this->processResponse($payment,$litleResponse);
	}
}

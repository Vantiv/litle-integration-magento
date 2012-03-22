<?php
require_once('Litle/LitleSDK/LitleOnline.php');

class Litle_LitleEcheck_Model_PaymentLogic extends Mage_Payment_Model_Method_Abstract
{
	/**
	 * unique internal payment method identifier
	 */
	protected $_code = 'litleecheck';

	protected $_formBlockType = 'litleecheck/form_litleEcheck';

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

	protected $dummy_fail = false;

	public function assignData($data)
	{
		if (!($data instanceof Varien_Object)) {
			$data = new Varien_Object($data);
		}

		$info = $this->getInfoInstance();
		$info->setAdditionalInformation('echeck_routing_num', $data->getEcheckRoutingNumber());
		$info->setAdditionalInformation('echeck_bank_acc_num', $data->getEcheckBankAcctNum());
		$info->setAdditionalInformation('echeck_acc_type', $data->getEcheckAccountType());

		return $this;
	}

	public function getConfigData($fieldToLookFor, $store = NULL)
	{
		$returnFromThisModel = Mage::getStoreConfig('payment/LitleEcheck/' . $fieldToLookFor);
		if( $returnFromThisModel == NULL )
		$returnFromThisModel = parent::getConfigData($fieldToLookFor, $store);

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
		if(!empty($contactInfo)){
			$retArray = array();
			if($this->dummy_fail)
			{
				$retArray["name"] = "Joe Green";
				$retArray["addressLine1"] = "6 Main St.";
				$retArray["city"] = "Derry";
				$retArray["state"] = "NH";
				$retArray["zip"] = "03038";
				$retArray["country"] = "US";
			}
			else{
				$retArray["firstName"] =$contactInfo->getFirstname();
				$retArray["lastName"] = $contactInfo->getLastname();
				$retArray["companyName"] = $contactInfo->getCompany();
				$retArray["addressLine1"] = $contactInfo->getStreet(1);
				$retArray["addressLine2"] = $contactInfo->getStreet(2);
				$retArray["addressLine3"] = $contactInfo->getStreet(3);
				$retArray["city"] = $contactInfo->getCity();
				$retArray["state"] = $contactInfo->getRegion();
				$retArray["zip"] = $contactInfo->getPostcode();
				$retArray["country"] = $contactInfo->getCountry();
				$retArray["email"] = $contactInfo->getCustomerEmail();
				$retArray["phone"] = $contactInfo->getTelephone();
			}
			return $retArray;
		}
		return NULL;
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

	public function merchantData(Varien_Object $payment)
	{
		$hash = array('user'=> $this->getConfigData("user"),
 					'password'=> $this->getConfigData("password"),
					'merchantId'=>$this->getConfigData("merchant_id"),
					'version'=>'8.10',
					'reportGroup'=>$this->getConfigData("reportGroup"),
					'url'=>'http://l-gdake-t5500:8081/sandbox/communicator/online',	
					'proxy'=>$this->getConfigData("proxy"),
					'timeout'=>$this->getConfigData("timeout")
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
					$payment
					->setStatus("Rejected")
					->setCcTransId(XMLParser::getNode($litleResponse,'litleTxnId'))
					->setLastTransId(XMLParser::getNode($litleResponse,'litleTxnId'))
					->setTransactionId(XMLParser::getNode($litleResponse,'litleTxnId'))
					->setIsTransactionClosed(0)
					->setTransactionAdditionalInfo(XMLParser::getNode($litleResponse,'message'));
				}
				else
				{
					$payment
					->setStatus("Approved")
					->setCcTransId(XMLParser::getNode($litleResponse,'litleTxnId'))
					->setLastTransId(XMLParser::getNode($litleResponse,'litleTxnId'))
					->setTransactionId(XMLParser::getNode($litleResponse,'litleTxnId'))
					->setIsTransactionClosed(0)
					->setTransactionAdditionalInfo(XMLParser::getNode($litleResponse,'message'));
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
		$orderId = $this->dummy_fail ? "6" : $order->getIncrementId();
		$amountToPass = $this->dummy_fail ? "60060" : ($amount* 100);

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
		$orderId = $this->dummy_fail ? "6" : $order->getIncrementId();
		$amountToPass = $this->dummy_fail ? "60060" : ($amount* 100);

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

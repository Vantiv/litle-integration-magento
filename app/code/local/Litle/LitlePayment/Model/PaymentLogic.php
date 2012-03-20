<?php
require_once('Litle/LitleSDK/LitleOnline.php');

class Litle_LitlePayment_Model_PaymentLogic extends Mage_Payment_Model_Method_Cc
{
	/**
	 * unique internal payment method identifier
	 */
	protected $_code = 'litlepayment';

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

	public function getConfigData($fieldToLookFor, $store = NULL)
	{
		$returnFromThisModel = Mage::getStoreConfig('payment/LitlePayment/' . $fieldToLookFor);
		if( $returnFromThisModel == NULL )
			$returnFromThisModel = parent::getConfigData($fieldToLookFor, $store);

		return $returnFromThisModel;
	}

	public function getCreditCardInfo(Varien_Object $payment)
	{
		$retArray = array();
		$retArray["type"] = $payment->getCcType();
		$retArray["number"] = $payment->getCcNumber();
		preg_match("/\d\d(\d\d)/", $payment->getCcExpYear(), $expYear);
		$retArray["expDate"] = sprintf('%02d%02d', $payment->getCcExpMonth(), $expYear[1]);
		$retArray["cardValidationNum"] = $payment->getCcCid();

		if($this->dummy_fail)
		{
			$retArray["type"] = "VI";
			$retArray["number"] = "4457010100000008";
			$retArray["expDate"] = "0612";
			$retArray["cardValidationNum"] = "992";
		}

		return $retArray;
	}
	//!########################## DELETE THIS DATA ####################################
	// 	new Auth(orderId: '6', amount: '60060', name: 'Joe Green', addressLine1: '6 Main St.', city: 'Derry',
	// 	state: 'NH', zip: '03038', country: 'US', type: 'VI', number: '4457010100000008', expDate: '0612',
	// 	cardValidationNum: '992', response: '110', message: 'Insufficient Funds', avsResult: '34',
	// 	cardValidationResult: 'P', returnLitleTxnId: '600000000000000001').save()

	// 	new Sale(orderId: '6', amount: '60060', name: 'Joe Green', addressLine1: '6 Main St.', city: 'Derry',
	// 	 state: 'NH', zip: '03038', country: 'US', type: 'VI', number: '4457010100000008', expDate: '0612',
	// 	 cardValidationNum: '992', response: '110', message: 'Insufficient Funds', avsResult: '34',
	// 	 cardValidationResult: 'P', returnLitleTxnId: '600000000000000002').save()


	// 	new Auth(orderId: '7', amount: '70070', name: 'Jane Murray', addressLine1: '7 Main St.', city: 'Amesbury',
	// 	state: 'MA', zip: '01913', country: 'US', type: 'MC', number: '5112010100000002', expDate: '0712',
	// 	cardValidationNum: '251', response: '301', message: 'Invalid Account Number', authCode: '', avsResult: '34',
	// 	 cardValidationResult: 'N', returnLitleTxnId: '700000000000000001').save()

	// 	new Auth(orderId: '7', amount: '000', name: 'Jane Murray', addressLine1: '7 Main St.', city: 'Amesbury',
	// 	state: 'MA', zip: '01913', country: 'US', type: 'MC', number: '5112010100000002', expDate: '0712',
	// 	cardValidationNum: '251', response: '301', message: 'Invalid Account Number', authCode: '',
	// 	avsResult: '34', cardValidationResult: 'N', returnLitleTxnId: '700000000000000002').save()

	// 	new Sale(orderId: '7', amount: '70070', name: 'Jane Murray', addressLine1: '7 Main St.', city: 'Amesbury',
	// 	state: 'MA', zip: '01913', country: 'US', type: 'MC', number: '5112010100000002', expDate: '0712',
	// 	cardValidationNum: '251', response: '301', message: 'Invalid Account Number', authCode: '', avsResult: '34',
	// 	 cardValidationResult: 'N', returnLitleTxnId: '700000000000000003').save()

	//!########################## DELETE THIS DATA ####################################

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
	/**
	 * this method is called if we are just authorising
	 * a transaction
	 */
	public function authorize (Varien_Object $payment, $amount)
	{
		Mage::throwException($this->getConfigData("api_key"));
		$order = $payment->getOrder();
		$orderId = $this->dummy_fail ? "6" : $order->getIncrementId();
		$amountToPass = $this->dummy_fail ? "60060" : ($amount* 100);
		
		if (!empty($order)){
			$hash_in = array(
	 					'orderId'=> $orderId,
	 					'amount'=> $amountToPass,
	 					'orderSource'=> "ecommerce",
						'billToAddress'=> $this->getBillToAddress($payment),
						'shipToAddress'=> $this->getAddressInfo($payment),
	 					'card'=> $this->getCreditCardInfo($payment)
			);
			$litleRequest = new LitleOnlineRequest();
			$litleResponse = $litleRequest->authorizationRequest($hash_in);
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
					//->setCcApproval("Approved")
					//->setAddressResult(XmlParser::getNode($litleResponse,'avsResult'))
					//->setCv2Result(XmlParser::getNode($litleResponse,'cardValidationResult'));
				}
				return $this;
			}
		}
	}

	/**
	 * this method is called if we are authorising AND
	 * capturing a transaction
	 */
	public function capture (Varien_Object $payment, $amount)
	{
		$order = $payment->getOrder();
		if (!empty($order)){
			$hash_in = array(
							'litleTxnId' => $payment->getCcTransId()
			);
			$litleRequest = new LitleOnlineRequest();
			$litleResponse = $litleRequest->captureRequest($hash_in);
			//Mage::throwException($litleResponse->saveXML());
		}
			
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

	/**
	 * called if refunding
	 */
	public function refund (Varien_Object $payment, $amount)
	{

	}

	/**
	 * called if voiding a payment
	 */
	public function void (Varien_Object $payment)
	{

	}
}

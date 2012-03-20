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

	public function validate()
	{
		return $this;
	}

	public function assignData($data)
	{
		//echo Varien_Debug::backtrace(true, true); exit;
		if (!($data instanceof Varien_Object)) {
			$data = new Varien_Object($data);
		}

		$info = $this->getInfoInstance();
		$info->setAdditionalInformation('echeck_routing_num', $data->getEcheckRoutingNumber());
		//$info->setAdditionalInformation('echeck_bank_name', $data->getEcheckBankName());
		$info->setAdditionalInformation('echeck_bank_acc_num', $data->getEcheckBankAcctNum());
		$info->setAdditionalInformation('echeck_acc_type', $data->getEcheckAccountType());
		//$info->setAdditionalInformation('echeck_acc_name', $data->getEcheckAccountName());
		return $this;
	}
	
	public function getConfigData($fieldToLookFor, $store)
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

		$retArray["accType"] = "Checking";//$info->getAdditionalInformation('echeck_acc_type');

		$retArray["routingNum"] = $info->getAdditionalInformation('echeck_routing_num');

		return $retArray;

	}

	public function getBillToAddress(Varien_Object $payment)
	{
		$order = $payment->getOrder();
		if(!empty($order)){
			$billing = $order ->getBillingAddress();
			if(!empty($billing)){
				$retArray = array();
				$retArray["firstName"] =$billing->getFirstname();
				$retArray["lastName"] = $billing->getLastname();
				$retArray["companyName"] = $billing->getCompany();
				$retArray["addressLine1"] = $billing->getStreet(1);
				$retArray["addressLine2"] = $billing->getStreet(2);
				$retArray["addressLine3"] = $billing->getStreet(3);
				$retArray["city"] = $billing->getCity();
				$retArray["state"] = $billing->getRegion();
				$retArray["zip"] = $billing->getPostcode();
				$retArray["country"] = $billing->getCountry();
				$retArray["email"] = $billing->getCustomerEmail();
				$retArray["phone"] = $billing->getTelephone();
				return $retArray;
			}
		}
	}

	public function getShipToAddress(Varien_Object $payment)
	{
		$order = $payment->getOrder();
		if(!empty($order)){
			$shipping = $order->getShippingAddress();
			if(!empty($shipping)){
				$retArray = array();
				$retArray["firstName"] = $shipping->getFirstname();
				$retArray["lastName"] = $shipping->getLastname();
				$retArray["companyName"] = $shipping->getCompany();
				$retArray["addressLine1"] = $shipping->getStreet(1);
				$retArray["addressLine2"] = $shipping->getStreet(2);
				$retArray["addressLine3"] = $shipping->getStreet(3);
				$retArray["city"] = $shipping->getCity();
				$retArray["state"] = $shipping->getRegion();
				$retArray["zip"] = $shipping->getPostcode();
				$retArray["country"] = $shipping->getCountry();
				$retArray["email"] = $shipping->getCustomerEmail();
				$retArray["phone"] = $shipping->getTelephone();
				return $retArray;
			}
		}
	}
	
	/**
	 * this method is called if we are just authorising
	 * a transaction
	 */
	public function authorize (Varien_Object $payment, $amount)
	{
		//echo Mage::getStoreConfig('payment/LitleEcheck/active'); exit;
		$order = $payment->getOrder();
		$orderId = $order->getIncrementId();
		$amountToPass = $amount* 100;
		
		if (!empty($order)){
			$hash_in = array(
	 					'orderId'=> $orderId,
	 					'amount'=> $amountToPass,
	 					'orderSource'=> "ecommerce",
						'billToAddress'=> $this->getBillToAddress($payment),
						'shipToAddress'=> $this->getAddressInfo($payment),
	 					'echeck'=> $this->getEcheckInfo($payment)
			);
			$litleRequest = new LitleOnlineRequest();
			$litleResponse = $litleRequest->echeckVerificationRequest($hash_in);
			//Mage::throwException($response);
			//Mage::throwException(XmlParser::getAttribute($litleResponse,'litleOnlineResponse','message'));
			//Mage::throwException(XmlParser::getNode($litleResponse,'message'));
			//Mage::throwException(XmlParser::getNode($response,'litleTxnId'));
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
			$litleResponse = $litleRequest->echeckSaleRequest($hash_in);
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

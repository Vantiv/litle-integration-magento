<?php
require_once('LitleOnline.php');

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

	public function getCreditCardInfo(Varien_Object $payment)
	{
		$retArray = array();
		$retArray["type"] = $payment->getCcType();
		$retArray["number"] = $payment->getCcNumber();
		preg_match("/\d\d(\d\d)/", $payment->getCcExpYear(), $expYear);
		$retArray["expDate"] = sprintf('%02d%02d', $payment->getCcExpMonth(), $expYear[1]);
		$retArray["cardValidationNum"] = $payment->getCcCid();
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
		$order = $payment->getOrder();
		if (!empty($order)){
			$hash_in = array(
	 					'orderId'=> $order->getIncrementId(),
	 					'amount'=> ($amount* 100),
	 					'orderSource'=> "ecommerce",
						'billToAddress'=> $this->getBillToAddress($payment),
						'shipToAddress'=> $this->getAddressInfo($payment),
	 					'card'=> $this->getCreditCardInfo($payment)
			);
			$litleRequest = new LitleOnlineRequest();
			$litleResponse = $litleRequest->authorizationRequest($hash_in);
			//Mage::throwException($response);
			//Mage::throwException(XmlParser::getAttribute($response,'litleOnlineResponse','message'));
			//Mage::throwException(XmlParser::getNode($response,'message'));
			//Mage::throwException(XmlParser::getNode($response,'litleTxnId'));
			if( isset($litleResponse))
			{
				$litleResponseCode = XMLParser::getNode($litleResponse,'response');
				if($litleResponseCode != "000")
				{
					// 					$order_status_id = 1;
					// 					if( $latest_order_history ){
					// 						$order_status_id = $latest_order_history[0]['order_status_id'];
					// 					}
				}
				else
				{
					$payment
					->setStatus("Approved")
					->setCcTransId(XMLParser::getNode($litleResponse,'litleTxnId'))
					->setLastTransId(XMLParser::getNode($litleResponse,'litleTxnId'))
					->setCcApproval("Approved");
					//->setAddressResult($result->getAddressResult())
					//->setPostcodeResult($result->getPostCodeResult())
					//->setCv2Result($result->getCV2Result())
					//->setCcCidStatus($result->getTxAuthNo())
					//->setSecurityKey($result->getSecurityKey());
				}
				//$comment = $litleTxtToInsertInComment . ": " . XMLParser::getNode($litleResponse,'message') . " \n ". $this->language->get('text_litle_response_code') . " " . $litleResponseCode . "\n ". $this->language->get('text_litle_transaction_id'). " " . XMLParser::getNode($litleResponse,'litleTxnId');

				// 				$data = array(
				// 									'order_status_id'=>$order_status_id,
				// 									'comment'=>$comment
				// 				);

				// 				$this->model_sale_order->addOrderHistory($order_id, $data);
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
			// 			$hash_in = array(
			// 			 					'orderId'=> $order->getIncrementId(),
			// 			 					'amount'=> ($amount* 100),
			// 			 					'orderSource'=> "ecommerce",
			// 								'billToAddress'=> $this->getBillToAddress($payment),
			// 								'shipToAddress'=> $this->getAddressInfo($payment),
			// 			 					'card'=> $this->getCreditCardInfo($payment)
			// 			);
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
				// 					$order_status_id = 1;
				// 					if( $latest_order_history ){
				// 						$order_status_id = $latest_order_history[0]['order_status_id'];
				// 					}
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
				//->setCcApproval("Approved");
				// 					->setAddressResult($result->getAddressResult())
				// 					->setPostcodeResult($result->getPostCodeResult())
				// 					->setCv2Result($result->getCV2Result())
				// 					->setCcCidStatus($result->getTxAuthNo())
				// 					->setSecurityKey($result->getSecurityKey());
			}
			
			return $this;
			//$comment = $litleTxtToInsertInComment . ": " . XMLParser::getNode($litleResponse,'message') . " \n ". $this->language->get('text_litle_response_code') . " " . $litleResponseCode . "\n ". $this->language->get('text_litle_transaction_id'). " " . XMLParser::getNode($litleResponse,'litleTxnId');

			// 				$data = array(
			// 									'order_status_id'=>$order_status_id,
			// 									'comment'=>$comment
			// 				);

			// 				$this->model_sale_order->addOrderHistory($order_id, $data);
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

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

	
	
	
	
	
	
	
	
	
// 	public function getAddressInfo($order_info, $addressType)
// 	{
// 		$retArray = array();
// 		$retArray["firstName"] = XMLFields::returnArrayValue($order_info, ($addressType . "_firstname") );
// 		//$retArray["middleInitial"]= XMLFields::returnArrayValue($hash_in, ($addressType . "_lastname") );
// 		$retArray["lastName"] = XMLFields::returnArrayValue($order_info, ($addressType . "_lastname") );
// 		//$retArray["name"] = XMLFields::returnArrayValue($order_info, ($addressType . "_firstname") );
// 		$retArray["companyName"] = XMLFields::returnArrayValue($order_info, ($addressType . "_company") );
// 		$retArray["addressLine1"] = XMLFields::returnArrayValue($order_info, ($addressType . "_address_1") );
// 		$retArray["addressLine2"] = XMLFields::returnArrayValue($order_info, ($addressType . "_address_2") );
// 		$retArray["city"] = XMLFields::returnArrayValue($order_info, ($addressType . "_city") );
// 		$retArray["state"] = XMLFields::returnArrayValue($order_info, ($addressType . "_firstname") );
// 		$retArray["zip"] = XMLFields::returnArrayValue($order_info, ($addressType . "_postcode") );
// 		//$retArray["country"] = XMLFields::returnArrayValue($order_info, ($addressType . "_country") );
// 		$retArray["country"] = XMLFields::returnArrayValue($order_info, ($addressType . "_iso_code_2") );
// 		$retArray["email"] = XMLFields::returnArrayValue($order_info, "email" );
// 		$retArray["phone"] = XMLFields::returnArrayValue($order_info, "telephone" );
// 		return $retArray;
// 	}
	
	public function getCreditCardInfo()
	{
		//$this->load->model('checkout/order');
		//$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
	
// 		$retArray = array();
// 		$retArray["type"] = $this->request->post['cc_type'];
// 		$retArray["number"] = str_replace(' ', '', $this->request->post['cc_number']);
// 		//TODO: fix the logic for expDate
// 		$retArray["expDate"] = $this->request->post['cc_expire_date_month'] . ($this->request->post['cc_expire_date_year']-2000);
// 		$retArray["cardValidationNum"] = $this->request->post['cc_cvv2'];

		$retArray = array();
		$retArray["type"] = "VI";
		$retArray["number"] = "4100000000000001";
		//TODO: fix the logic for expDate
		$retArray["expDate"] = "1125";
		$retArray["cardValidationNum"] = "369";
			
		return $retArray;
	}
	
// 	public function getAmountInCorrectFormat($amount) {
// 		$retVal = str_replace(",", '', $amount);
// 		$posOfDot = strpos($retVal, ".");
// 		if($posOfDot != FALSE){
// 			$retVal = substr($retVal, 0, $posOfDot + 3);
// 			$retVal = str_replace(".", '', $retVal);
// 		}
// 		return $retVal;
// 	}
	
// 	public function send() {
// 		$this->load->model('checkout/order');
// 		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
	
// 		$orderAmountToInsert = $this->getAmountInCorrectFormat($order_info['total']);
// 		$hash_in = array(
// 	 					'orderId'=> $order_info['order_id'],
// 	 					'amount'=> $orderAmountToInsert,
// 	 					'orderSource'=> "ecommerce",
// 	 					'billToAddress'=> $this->getAddressInfo($order_info, "payment"),
// 	 					'shipToAddress'=> $this->getAddressInfo($order_info, "shipping"),
// 	 					'card'=> $this->getCreditCardInfo(),
// 		);
// 		$litleResponseMessagePrefix = "";
// 		$litleRequest = new LitleOnlineRequest();
// 		$doingAuth = $this->config->get('litle_transaction') == "auth";
// 		if($doingAuth) {
// 			//auth txn
// 			$response = $litleRequest->authorizationRequest($hash_in);
// 			$litleResponseMessagePrefix = "LitleAuthTxn: ";
// 		}
// 		else {
// 			//sale txn
// 			$response = $litleRequest->saleRequest($hash_in);
// 			$litleResponseMessagePrefix = "LitleCaptureTxn: ";
// 		}
	
// 		$code = XMLParser::getNode($response, "response");
// 		$litleValidationMessage = XMLParser::getNode($response, "message");
// 		$litleTxnId = XMLParser::getNode($response, "litleTxnId");
	
// 		$json = array();
// 		if($code == "000") {
// 			//Success
// 			if($doingAuth) {
// 				$orderStatusId = 1; //Pending
// 			}
// 			else {
// 				$orderStatusId = 5; //Processing
// 			}
// 			$message = $litleResponseMessagePrefix . $litleValidationMessage . " \n Litle Response Code: " . $code . "\n  Litle Transaction ID: " . $litleTxnId . " \n";
// 			$json['success'] = $this->url->link('checkout/success', '', 'SSL');
// 		}
// 		else if($code == "100" || $code == "101" || $code == "102" || $code == "110"){
// 			//Need to try again
// 			$orderStatusId = 10; //Failed
// 			$litleResponseMessagePrefix = "LitleTxn: ";
// 			$message = $litleResponseMessagePrefix . $litleValidationMessage . " \n Litle Response Code: " . $code . "\n  Litle Transaction ID: " . $litleTxnId . " \n";
// 			$json['error'] = "Either your credit card was declined or there was an error. Please try again or contact us for further help.";
// 		}
// 		else {
// 			$xpath = new DOMXPath($response);
// 			$query = 'string(/litleOnlineResponse/@message)';
// 			$message = $xpath->evaluate($query);
// 			$orderStatusId = 8; //Denied
// 			$json['error'] = "Either your credit card was declined or there was an error. Please try again or contact us for further help.";
// 		}
	
// 		$this->model_checkout_order->confirm(
// 		$order_info['order_id'],
// 		$orderStatusId,
// 		$message,
// 		true
// 		);
	
// 		$this->response->setOutput(json_encode($json));
// 	}
	
	
	
	
	
	
	
	
	/**
	 * this method is called if we are just authorising
	 * a transaction
	 */
	public function authorize (Varien_Object $payment, $amount)
	{
		//sleep(20);
		$hash_in = array(
	 					'orderId'=> "2135",
	 					'amount'=> "200000",
	 					'orderSource'=> "ecommerce",
	 					//'billToAddress'=> $this->getAddressInfo($order_info, "payment"),
	 					//'shipToAddress'=> $this->getAddressInfo($order_info, "shipping"),
	 					'card'=> $this->getCreditCardInfo(),
		);
		$litleRequest = new LitleOnlineRequest();
		$response = $litleRequest->authorizationRequest($hash_in);
		Mage::throwException("LitleTxnId: " . XMLParser::getNode($response, "litleTxnId"));
		//$litleResponseMessagePrefix = "LitleAuthTxn: ";
	}

	/**
	 * this method is called if we are authorising AND
	 * capturing a transaction
	 */
	public function capture (Varien_Object $payment, $amount)
	{

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

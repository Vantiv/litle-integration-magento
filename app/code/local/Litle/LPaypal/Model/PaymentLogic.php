<?php
require_once ('Litle/LitleSDK/LitleOnline.php');

class Litle_LPaypal_Model_PaymentLogic extends Mage_Payment_Model_Method_Abstract
{

    /**
     * unique internal payment method identifier
     */
    protected $_code = 'lpaypal';

    // protected $_formBlockType = 'creditcard/form_creditCard';

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
        $returnFromThisModel = Mage::getStoreConfig('payment/LPaypal/' . $fieldToLookFor);
        if (is_null($returnFromThisModel)) {
            $returnFromThisModel = Mage::getStoreConfig('payment/CreditCard/' . $fieldToLookFor);
            if (is_null($returnFromThisModel)){
                $returnFromThisModel = parent::getConfigData($fieldToLookFor, $store);
            }
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
                'merchantSdk' => 'Magento;8.15.4',
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
     *
     * @param Varien_Object $payment
     * @param DOMDocument $litleResponse
     * @throws Mage_Payment_Model_Info_Exception
     * @return boolean
     */
    public function processResponse(Varien_Object $payment, $litleResponse, $amount=0, $closeOrder=false)
    {
//         $this->accountUpdater($payment, $litleResponse);
        $message = XmlParser::getAttribute($litleResponse, 'litleOnlineResponse', 'message');
        if ($message == 'Valid Format') {
             $orderTransaction = $payment->lookupTransaction(false, Mage_Sales_Model_Order_Payment_Transaction::TYPE_ORDER);
            if (isset($litleResponse)) {
                $litleResponseCode = XMLParser::getNode($litleResponse, 'response');
                if ($litleResponseCode != '000') {
                     if (!$orderTransaction) {
                         if(Mage::getStoreConfig('payment/CreditCard/debug_enable')) {
                             Mage::log("Had an unsuccessful response in an authorization/sale - response code: " . $litleResponseCode, null, "litle.log");
                         }
                         $errorInfoForFrontend="The order was not approved.  Please try again later or contact us.  For your reference, the transaction id is ";
                         $customerId = $payment->getOrder()->getCustomerId();
                         $orderId = $payment->getOrder()->getId();
                         Mage::helper('creditcard')->writeFailedTransactionToDatabase($customerId, null, $message, $litleResponse); //null order id because the order hasn't been created yet

                         Mage::throwException( $errorInfoForFrontend. XMLParser::getNode($litleResponse, 'litleTxnId'));
                        
                     }
                     else {
                         $this->handleResponseForNonSuccessfulBackendTransactions($payment, $litleResponse, $litleResponseCode);
                     }
                } else {
                    // process the previous order transaction:
                    // 1. close the order transaction if the grand total amount of the order has been paid.
                    // 2. add the order transaction if we are doing a frontend transaction when the order transaction
                    //    hasn't been stored in the database.
//                    $orderTransaction = $payment->lookupTransaction(false, Mage_Sales_Model_Order_Payment_Transaction::TYPE_ORDER);
                    if($orderTransaction){
                        if ($closeOrder){
                            $orderTransaction->close(true);
                        }
                    }else{
                        if ($closeOrder){
                            $payment->setIsTranlsactionClosed(1);
                        }
                        // add paypal order transaction
                        $formattedPrice = $payment->getOrder()->getBaseCurrency()->formatTxt($amount);
                        if ($payment->getIsTransactionPending()) {
                            $order_message = Mage::helper('paypal')->__('Ordering amount of %s is pending approval on gateway.', $formattedPrice);
                        } else {
                            $order_message = Mage::helper('paypal')->__('Ordered amount of %s.', $formattedPrice);
                        }
                        $payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_ORDER, null, false, $order_message);
                        $payment->setParentTransactionId($payment->getLastTransId());
                    }
                    // prepare new litle transaction information in the payment
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
    
    public function handleResponseForNonSuccessfulBackendTransactions(Varien_Object $payment, $litleResponse, $litleResponseCode) {
        $order = $payment->getOrder();
        $litleMessage = XMLParser::getNode($litleResponse, 'message');
        $litleTxnId = XMLParser::getNode($litleResponse, 'litleTxnId');
        $customerId = $order->getData('customer_id');
        $orderId = $order->getId();
        

        // TODO: Add sandbox test for the responseCode of Paypal transaction
        if($litleResponseCode === '120') {
            $descriptiveMessage = "Either an internal PayPal error occurred, the maximum number of authorizations allowed for the transaction is reached.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID);
        }
        elseif($litleResponseCode === '127') {
            $descriptiveMessage = "This transaction exceeds the daily approval limit for the card or the PayPal user account.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID);
        }
        elseif($litleResponseCode === '328') {
            $descriptiveMessage = "Transaction Not Voided - Already Settled.  This transaction cannot be voided; it has already been delivered to the card networks.  You may want to try a refund instead.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID);
        }
        elseif($litleResponseCode === '350') {
            $descriptiveMessage = "There is an unspecified problem contact the issuing bank (This is the default Response Code for any undefined PayPal code.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND);
        }
        elseif($litleResponseCode === '601') {
            $descriptiveMessage = "The transaction failed due to an issue with primary funding source (e.g.expired Card, insufficient funds, etc.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '602') {
            $descriptiveMessage = "The merchant may resubmit the transaction immediately and the use of an alternate funding source will be attempted.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '610') {
            $descriptiveMessage = "The billing agreement id is invalid.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '611') {
            $descriptiveMessage = "issuer is unavailable.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '612') {
            $descriptiveMessage = "the transaction failed due to an issue with the buyer account.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '613') {
            $descriptiveMessage = "A paypal response indicating the need to correct the auth id before resubmitting.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '614') {
            $descriptiveMessage = "A paypal response indicating your account is configured to decline transactions without a confirmed address. request another payment method or contact support@litle.com to modify your account settings.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '615') {
            $descriptiveMessage = "a paypal response indicating account unauthorized payment risk.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '616') {
            $descriptiveMessage = "a paypal response indicating paypal is unable to process the payment. buyer should contact paypal with questions.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '617') {
            $descriptiveMessage =  "paypal response indicating no further auths/captures can be processin against this order". $order->getIncrementId()." a new order must be created.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE);
        }
        elseif($litleResponseCode === '618') {
            $descriptiveMessage = "A PayPal response indicating one of these potential refund-related issues: duplicate, partial refund must be less than or equal to original or remaining amount, past time limit, not allowed for transaction type, consumer account locked/inactive, or complaint exists - only a full refund of total/remaining amount allowed. Contact support@litle.com for specific details.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '619') {
            $descriptiveMessage = "A PayPal response indicating you do not have permissions to make this API call.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '620') {
            $descriptiveMessage = "A PayPal response indicating you cannot capture against this authorization. You need to perform a brand new authorization for the transaction.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '621') {
            $descriptiveMessage = "A PayPal response indicating missing parameters are required. Contact support@litle.com for specific details.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '622') {
            $descriptiveMessage = "A PayPal response indicating the need to check the validity of the authorization ID prior to reattempting the transaction.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '623') {
            $descriptiveMessage = "A PayPal response indicating you should capture against a previous authorization.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '624') {
            $descriptiveMessage = "A paypal response indicating the transaction amount exceeds the merchants account limit. Contact support@litle.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '625') {
            $descriptiveMessage = "A PayPal response indicating the buyer needs to add another funding sources to their account.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '626') {
            $descriptiveMessage = "A PayPal response indicating there are issues with the buyer?s primary funding source.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '627') {
            $descriptiveMessage = "Contact us to adjust your PayPal merchant profile preferences.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '628') {
            $descriptiveMessage = "There is a problem with the username and password. Contactsupport@litle.com.";
            $this->showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $descriptiveMessage, $litleTxnId, Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT);
        }
        elseif($litleResponseCode === '629') {
            $descriptiveMessage = "A PayPal response indicating that you must contact the consumer for another payment method.";
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
         $transaction = $paymentHelp->addTransaction($transactionType, null, true, $litleMessage);
         $payment->setStatus($paymentStatus)
             ->setCcTransId($litleTxnId)
             ->setLastTransId($litleTxnId)
             ->setTransactionId($litleTxnId)
             ->setIsTransactionClosed($closed)
             ->setTransactionAdditionalInfo('additional_information', $litleMessage);
    }
    
    public function showErrorForFailedTransaction($customerId, $orderId, $litleMessage, $litleResponse, $messageToShow, $litleTxnId, $txnType) {
        Mage::helper('creditcard')->writeFailedTransactionToDatabase($customerId, $orderId, $litleMessage, $litleResponse, $txnType);
        $resource = Mage::getSingleton('core/resource');
        $conn = $resource->getConnection('core_read');
        $query = 'select failed_transactions_id from litle_failed_transactions where litle_txn_id = ' . $litleTxnId;
        $failedTransactionId = $conn->fetchOne($query);
        $url = Mage::getUrl('palorus/adminhtml_myform/failedtransactionsview/') . 'failed_transactions_id/' . $failedTransactionId;
        Mage::throwException("Litle response message:" . $litleMessage . ".\nDescription:" . $messageToShow . " For your reference, the transaction id is <a href='" . $url . "'>".$litleTxnId."</a>");
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
                 $this->processResponse($payment, $litleResponse, $amount);
                 Mage::helper('palorus')->saveCustomerInsight($payment, $litleResponse);
             }
         }

         return $this;
     }
    
     function generateAuthorizationHash($orderId, $amountToPass, $info, $payment) {
         $hash = array(
                 'orderId' => $orderId,
                 'id' => $orderId,
                 'amount' => $amountToPass,
                 'paypal' => $this->_getPayPalInfo($payment),
                 'orderSource' => $info->getAdditionalInformation('orderSource'),
                 'billToAddress' => $this->getBillToAddress($payment),
                 'shipToAddress' => $this->getAddressInfo($payment),
                 'cardholderAuthentication' => $this->getFraudCheck($payment),
                 'enhancedData' => $this->getEnhancedData($payment),
                 'customBilling' => $this->getCustomBilling(
                         Mage::app()->getStore()
                             ->getBaseUrl())
         );
        
         $merchantData = $this->merchantData($payment);
         $hash_in = array_merge($hash, $merchantData);
         return $hash_in;
     }

    private function _getPayPalInfo($payment)
    {
        $retArray = array();
        $retArray["payerId"] = $payment->getAdditionalInformation(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_PAYER_ID);
        $retArray["token"] = $payment->getAdditionalInformation(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_TOKEN);
        // if this is the first transaction, then it must be the order transaction
        $txnID = $payment->getTransactionId();
        $orderTransaction = $payment->lookupTransaction(false, Mage_Sales_Model_Order_Payment_Transaction::TYPE_ORDER);
        if($orderTransaction){
            $txnID = $orderTransaction->getTxnId();
        }
        $retArray["transactionId"] = $txnID;
        return $retArray;
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

            // check if it's a partial capture
            $isPartialCapture = ($amount < $order->getGrandTotal()) ? 'true' : 'false';
            $isOrderCompleted = ($payment->getAmountPaid() + $amount + 0.0001 < $order->getGrandTotal()) ? 'false' : 'true';

            // check if we should do a sale or a capture
            $orderTransaction = $payment->lookupTransaction(false, Mage_Sales_Model_Order_Payment_Transaction::TYPE_ORDER);
            $authTransaction = $payment->lookupTransaction(false, Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
            if($authTransaction){
                $isSale = Mage::helper('lpaypal')->isLastAuthCreatedBeforeDays($authTransaction, 3);
            } else{
                $isSale = true;
            }

            if (! $isSale) {
                $lastTxnId = $authTransaction->getTxnId();
                $payment->setParentTransactionId($lastTxnId);
                $hash = array(
                        'litleTxnId' => $lastTxnId,
                        'amount' => $amountToPass,
                        'payPalOrderComplete' => $isOrderCompleted,
                        'partial' => $isPartialCapture
                );
            } else {
                // if this is a frontend sale, the order transaction is not in the database yet
                // we get the order transaction id from $payment object
                $lastTxnId = $payment->getTransactionId();
                if($orderTransaction){
                    $lastTxnId = $orderTransaction->getTxnId();
                }
                $payment->setParentTransactionId($lastTxnId);
                $hash = array(
                        'orderId' => $orderId,
                        'amount' => $amountToPass,
                        'orderSource' => 'ecommerce',
                        'paypal' => $this->_getPayPalInfo($payment),
                        'payPalOrderComplete' => 'true'
                );
            }
            
            $merchantData = $this->merchantData($payment);
            $hash_in = array_merge($hash, $merchantData);

            // do Litle transaction
            $litleRequest = new LitleOnlineRequest();
            if ($isSale) {
                $litleResponse = $litleRequest->saleRequest($hash_in);
            } else {
                $litleResponse = $litleRequest->captureRequest($hash_in);
            }

            Mage::helper('palorus')->saveCustomerInsight($payment, $litleResponse);
            $this->processResponse($payment, $litleResponse, $amount, $isOrderCompleted == 'true');
        }
        return $this;
    }

    /**
     * called if refunding
     */
    public function refund(Varien_Object $payment, $amount)
    {
        $this->isFromVT($payment, 'refund');

        $order = $payment->getOrder();

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
            $merchantData = $this->merchantData($payment);
            $litleRequest = new LitleOnlineRequest();

            $authTransaction = $payment->lookupTransaction(false, Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
            if ($authTransaction && $payment->getAmountPaid() == 0) {
                $authTxnId = $authTransaction->getTxnId();
                $payment->setParentTransactionId($authTxnId);
                $hash = array(
                    'litleTxnId' => $authTxnId,
                    'payPalNotes' => ''
                );
                $hash_in = array_merge($hash, $merchantData);

                $litleResponse = $litleRequest->authReversalRequest($hash_in);
            } else {
                $lastTxnId = $payment->getLastTransId();
                $payment->setParentTransactionId($lastTxnId);
                $hash = array(
                    'litleTxnId' => $lastTxnId,
                );
                $hash_in = array_merge($hash, $merchantData);

                $litleResponse = $litleRequest->voidRequest($hash_in);
            }
            $this->processResponse($payment, $litleResponse);
        }
        return $this;
    }

    public function cancel(Varien_Object $payment)
    {
        $this->void($payment);
        return $this;
    }
}

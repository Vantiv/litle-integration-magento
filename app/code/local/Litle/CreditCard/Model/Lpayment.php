<?php

class Litle_CreditCard_Model_Lpayment extends Mage_Sales_Model_Order_Payment
{

    /**
     * Cancel specified invoice: update self totals from it
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return Mage_Sales_Model_Order_Payment
     */
    public function cancelInvoice($invoice)
    {
        $this->_updateTotals(array(
            'amount_paid' => -1 * $invoice->getGrandTotal(),
            'base_amount_paid' => -1 * $invoice->getBaseGrandTotal(),
            'shipping_captured' => -1 * $invoice->getShippingAmount(),
            'base_shipping_captured' => -1 * $invoice->getBaseShippingAmount(),
        ));
        Mage::dispatchEvent('sales_order_payment_cancel_invoice', array('payment' => $this, 'invoice' => $invoice));
        return $this;
    }

    /**
     * Void payment online
     *
     * @see self::_void()
     * @param Varien_Object $document
     * @return Mage_Sales_Model_Order_Payment
     */
    public function void(Varien_Object $document)
    {
        $this->_void(true);
        Mage::dispatchEvent('sales_order_payment_void', array('payment' => $this, 'invoice' => $document));
        return $this;
    }

    /**
     * Cancel a creditmemo: substract its totals from the payment
     *
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return Mage_Sales_Model_Order_Payment
     */
    public function cancelCreditmemo($creditmemo)
    {
        $this->_updateTotals(array(
            'amount_refunded' => -1 * $creditmemo->getGrandTotal(),
            'base_amount_refunded' => -1 * $creditmemo->getBaseGrandTotal(),
            'shipping_refunded' => -1 * $creditmemo->getShippingAmount(),
            'base_shipping_refunded' => -1 * $creditmemo->getBaseShippingAmount()
        ));
        Mage::dispatchEvent('sales_order_payment_cancel_creditmemo',
            array('payment' => $this, 'creditmemo' => $creditmemo)
        );
        return $this;
    }

    /**
     * Order cancellation hook for payment method instance
     * Adds void transaction if needed
     * @return Mage_Sales_Model_Order_Payment
     */
    public function cancel()
    {
        $isOnline = true;
        if (!$this->canVoid(new Varien_Object())) {
            $isOnline = false;
        }

        if (!$this->hasMessage()) {
            $this->setMessage($isOnline ? Mage::helper('sales')->__('Canceled order online.')
                : Mage::helper('sales')->__('Canceled order offline.')
            );
        }

        if ($isOnline) {
            $this->_void($isOnline, null, 'cancel');
        }

        Mage::dispatchEvent('sales_order_payment_cancel', array('payment' => $this));

        return $this;
    }

    /**
     * Authorize payment either online or offline (process auth notification)
     * Updates transactions hierarchy, if required
     * Prevents transaction double processing
     * Updates payment totals, updates order status and adds proper comments
     *
     * @param bool $isOnline
     * @param float $amount
     * @return Mage_Sales_Model_Order_Payment
     */
    protected function _authorize($isOnline, $amount)
    {
        // update totals
        $amount = $this->_formatAmount($amount, true);
        $this->setBaseAmountAuthorized($amount);

        // do authorization
        $order  = $this->getOrder();
        $state  = Mage_Sales_Model_Order::STATE_PROCESSING;
        $status = true;
        if ($isOnline) {
            // invoke authorization on gateway
            $this->getMethodInstance()->setStore($order->getStoreId())->authorize($this, $amount);
        } else {
            $message = Mage::helper('sales')->__(
                'Registered notification about authorized amount of %s.',
                $this->_formatPrice($amount)
            );
        }

        // similar logic of "payment review" order as in capturing
        if ($this->getIsTransactionPending()) {
            $message = Mage::helper('sales')->__('Authorizing amount of %s is pending approval on gateway.', $this->_formatPrice($amount));
            $state = Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW;
            if ($this->getIsFraudDetected()) {
                $status = Mage_Sales_Model_Order::STATUS_FRAUD;
            }
        } else {
            $message = Mage::helper('sales')->__('Authorized amount of %s.', $this->_formatPrice($amount));
        }

        // update transactions, order state and add comments
        $transaction = $this->_addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
        if ($order->isNominal()) {
            $message = $this->_prependMessage(Mage::helper('sales')->__('Nominal order registered.'));
        } else {
            $message = $this->_prependMessage($message);
            $message = $this->_appendTransactionToMessage($transaction, $message);
        }
        $order->setState($state, $status, $message);

        return $this;
    }

    /**
     * Public access to _authorize method
     * @param bool $isOnline
     * @param float $amount
     */
    public function authorize($isOnline, $amount)
    {
        return $this->_authorize($isOnline, $amount);
    }

    /**
     * Void payment either online or offline (process void notification)
     * NOTE: that in some cases authorization can be voided after a capture. In such case it makes sense to use
     *       the amount void amount, for informational purposes.
     * Updates payment totals, updates order status and adds proper comments
     *
     * @param bool $isOnline
     * @param float $amount
     * @param string $gatewayCallback
     * @return Mage_Sales_Model_Order_Payment
     */
    protected function _void($isOnline, $amount = null, $gatewayCallback = 'void')
    {
        $order = $this->getOrder();
        $authTransaction = $this->getAuthorizationTransaction();
        $this->_generateTransactionId(Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID, $authTransaction);
        $this->setShouldCloseParentTransaction(true);

        // attempt to void
        if ($isOnline) {
            $this->getMethodInstance()->setStore($order->getStoreId())->$gatewayCallback($this);
        }
        if ($this->_isTransactionExists()) {
            return $this;
        }

        // if the authorization was untouched, we may assume voided amount = order grand total
        // but only if the payment auth amount equals to order grand total
        if ($authTransaction && ($order->getBaseGrandTotal() == $this->getBaseAmountAuthorized())
            && (0 == $this->getBaseAmountCanceled())) {
            if ($authTransaction->canVoidAuthorizationCompletely()) {
                $amount = (float)$order->getBaseGrandTotal();
            }
        }

        if ($amount) {
            $amount = $this->_formatAmount($amount);
        }

        // update transactions, order state and add comments
        $transaction = $this->_addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID, null, true);
        if(true) //if voided transaction was a refund
        	$message = $this->hasMessage() ? $this->getMessage() : Mage::helper('sales')->__('Voided refund.');
        else	//if voided transaction was an auth
        	$message = $this->hasMessage() ? $this->getMessage() : Mage::helper('sales')->__('Voided authorization.');
        $message = $this->_prependMessage($message);
        if ($amount) {
            $message .= ' ' . Mage::helper('sales')->__('Amount: %s.', $this->_formatPrice($amount));
        }
        $message = $this->_appendTransactionToMessage($transaction, $message);
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $message);
        return $this;
    }

//    /**
//     * TODO: implement this
//     * @param Mage_Sales_Model_Order_Invoice $invoice
//     * @return Mage_Sales_Model_Order_Payment
//     */
//    public function cancelCapture($invoice = null)
//    {
//    }

    /**
     * Create transaction,
     * prepare its insertion into hierarchy and add its information to payment and comments
     *
     * To add transactions and related information,
     * the following information should be set to payment before processing:
     * - transaction_id
     * - is_transaction_closed (optional) - whether transaction should be closed or open (closed by default)
     * - parent_transaction_id (optional)
     * - should_close_parent_transaction (optional) - whether to close parent transaction (closed by default)
     *
     * If the sales document is specified, it will be linked to the transaction as related for future usage.
     * Currently transaction ID is set into the sales object
     * This method writes the added transaction ID into last_trans_id field of the payment object
     *
     * To make sure transaction object won't cause trouble before saving, use $failsafe = true
     *
     * @param string $type
     * @param Mage_Sales_Model_Abstract $salesDocument
     * @param bool $failsafe
     * @return null|Mage_Sales_Model_Order_Payment_Transaction
     */
    protected function _addTransaction($type, $salesDocument = null, $failsafe = false)
    {
        if ($this->getSkipTransactionCreation()) {
            $this->unsTransactionId();
            return null;
        }

        // look for set transaction ids
        $transactionId = $this->getTransactionId();
        if (null !== $transactionId) {
            // set transaction parameters
            $transaction = false;
            if ($this->getOrder()->getId()) {
                $transaction = $this->_lookupTransaction($transactionId);
            }
            if (!$transaction) {
                $transaction = Mage::getModel('sales/order_payment_transaction')->setTxnId($transactionId);
            }
            $transaction
                ->setOrderPaymentObject($this)
                ->setTxnType($type)
                ->isFailsafe($failsafe);

            if ($this->hasIsTransactionClosed()) {
                $transaction->setIsClosed((int)$this->getIsTransactionClosed());
            }

            //set transaction addition information
            if ($this->_transactionAdditionalInfo) {
                foreach ($this->_transactionAdditionalInfo as $key => $value) {
                    $transaction->setAdditionalInformation($key, $value);
                }
            }

            // link with sales entities
            $this->setLastTransId($transactionId);
            $this->setCreatedTransaction($transaction);
            $this->getOrder()->addRelatedObject($transaction);
            if ($salesDocument && $salesDocument instanceof Mage_Sales_Model_Abstract) {
                $salesDocument->setTransactionId($transactionId);
                // TODO: linking transaction with the sales document
            }

            // link with parent transaction
            $parentTransactionId = $this->getParentTransactionId();

            if ($parentTransactionId) {
                $transaction->setParentTxnId($parentTransactionId);
                if ($this->getShouldCloseParentTransaction()) {
                    $parentTransaction = $this->_lookupTransaction($parentTransactionId);
                    if ($parentTransaction) {
                        if (!$parentTransaction->getIsClosed()) {
                            $parentTransaction->isFailsafe($failsafe)->close(false);
                        }
                        $this->getOrder()->addRelatedObject($parentTransaction);
                    }
                }
            }
            return $transaction;
        }
    }

    /**
     * Public acces to _addTransaction method
     *
     * @param string $type
     * @param Mage_Sales_Model_Abstract $salesDocument
     * @param bool $failsafe
     * @param string $message
     * @return null|Mage_Sales_Model_Order_Payment_Transaction
     */
    public function addTransaction($type, $salesDocument = null, $failsafe = false, $message = false)
    {
        $transaction = $this->_addTransaction($type, $salesDocument, $failsafe);

        if ($message) {
            $order = $this->getOrder();
            $message = $this->_appendTransactionToMessage($transaction, $message);
            $order->addStatusHistoryComment($message);
        }

        return $transaction;
    }

    /**
     * Import details data of specified transaction
     *
     * @param Mage_Sales_Model_Order_Payment_Transaction $transactionTo
     * @return Mage_Sales_Model_Order_Payment
     */
    public function importTransactionInfo(Mage_Sales_Model_Order_Payment_Transaction $transactionTo)
    {
        $data = $this->getMethodInstance()
            ->setStore($this->getOrder()->getStoreId())
            ->fetchTransactionInfo($this, $transactionTo->getTxnId());
        if ($data) {
            $transactionTo->setAdditionalInformation(Mage_Sales_Model_Order_Payment_Transaction::RAW_DETAILS, $data);
        }
        return $this;
    }


    /**
     * Append transaction ID (if any) message to the specified message
     *
     * @param Mage_Sales_Model_Order_Payment_Transaction|null $transaction
     * @param string $message
     * @return string
     */
    protected function _appendTransactionToMessage($transaction, $message)
    {
        if ($transaction) {
            $txnId = is_object($transaction) ? $transaction->getTxnId() : $transaction;
            $message .= ' ' . Mage::helper('sales')->__('Transaction ID: "%s".', $txnId);
        }
        return $message;
    }

    /**
     * Lookup an authorization transaction using parent transaction id, if set
     * @return Mage_Sales_Model_Order_Payment_Transaction|false
     */
    public function getAuthorizationTransaction()
    {
        if ($this->getParentTransactionId()) {
            $txn = $this->_lookupTransaction($this->getParentTransactionId());
        } else {
            $txn = false;
        }

        if (!$txn) {
            $txn = $this->_lookupTransaction(false, Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
        }
        return $txn;
    }

    /**
     * Update transaction ids for further processing
     * If no transactions were set before invoking, may generate an "offline" transaction id
     *
     * @param string $type
     * @param Mage_Sales_Model_Order_Payment_Transaction $transactionBasedOn
     */
    protected function _generateTransactionId($type, $transactionBasedOn = false)
    {
        if (!$this->getParentTransactionId() && !$this->getTransactionId() && $transactionBasedOn) {
            $this->setParentTransactionId($transactionBasedOn->getTxnId());
        }
        // generate transaction id for an offline action or payment method that didn't set it
        if (($parentTxnId = $this->getParentTransactionId()) && !$this->getTransactionId()) {
            $this->setTransactionId("{$parentTxnId}-{$type}");
        }
    }
    
    /**
     * Additionnal transaction info setter
     *
     * @param sting $key
     * @param string $value
     */
    public function setTransactionAdditionalInfo($key, $value)
    {
        if (is_array($key)) {
            $this->_transactionAdditionalInfo = $key;
        } else {
            $this->_transactionAdditionalInfo[$key] = $value;
        }
    }

    /**
     * Additionnal transaction info getter
     *
     * @param sting $key
     * @return mixed
     */
    public function getTransactionAdditionalInfo($key = null)
    {
        if (is_null($key)) {
            return $this->_transactionAdditionalInfo;
        }
        return isset($this->_transactionAdditionalInfo[$key]) ? $this->_transactionAdditionalInfo[$key] : null;
    }

    /**
     * Reset transaction additional info property
     *
     * @return Mage_Sales_Model_Order_Payment
     */
    public function resetTransactionAdditionalInfo()
    {
        $this->_transactionAdditionalInfo = array();
        return $this;
    }

    /**
     * Return invoice model for transaction
     *
     * @param string $transactionId
     * @return Mage_Sales_Model_Order_Invoice
     */
    protected function _getInvoiceForTransactionId($transactionId)
    {
        foreach ($this->getOrder()->getInvoiceCollection() as $invoice) {
            if ($invoice->getTransactionId() == $transactionId) {
                $invoice->load($invoice->getId()); // to make sure all data will properly load (maybe not required)
                return $invoice;
            }
        }
        foreach ($this->getOrder()->getInvoiceCollection() as $invoice) {
            if ($invoice->getState() == Mage_Sales_Model_Order_Invoice::STATE_OPEN
                && $invoice->load($invoice->getId())
            ) {
                $invoice->setTransactionId($transactionId);
                return $invoice;
            }
        }
        return false;
    }
}

<?php

class Litle_Palorus_Block_Adminhtml_Palorus_Insight_Failedtransactionsview extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('litle/form/failedtransactions.phtml');
    }
    
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    private function getFailedTransactionRow(){
        $failureId = $this->getFailedTransactionsId();
        return Mage::getModel('palorus/failedtransactions')->getCollection()->addFieldToFilter('failed_transactions_id', $failureId);
    }

    private function getFailedTransactionData($field)
    {
        $collection = $this->getFailedTransactionRow();
        foreach ($collection as $failure){
            $row = $failure->getData();
            return $row[$field];
        }
    }
    
    public function updateFailedTransaction(){
        if($this->getFailedTransactionData('active')){
            $this->setActive(false);
        }
    }
    
    protected function setFailedTransactionsId($id){
        $this->failedTransactions = $id;
    }
    
     public function getFailedTransactionsId(){
         if ($this->failedTransactionsId === Null){
             $url = $this->helper("core/url")->getCurrentUrl();
             $stringAfterFailedTransactionsId = explode('failed_transactions_id/', $url);
             $stringBeforeKey = explode('/', $stringAfterFailedTransactionsId[1]);
             return $stringBeforeKey[0];
         }else {
             return $this->failedTransactionsId;
         }
     }
     
      public function getLitleTxnId(){
         return $this->getFailedTransactionData('litle_txn_id');
     }
     
     public function getMessage() {
     	return $this->getFailedTransactionData('message');
     }
     
     public function getFullXml() {
     	$orig =  $this->getFailedTransactionData('full_xml');
     	$converted = htmlentities($orig);
     	$newLinesBecomeBreaks = str_replace("\n","<br/>", $converted);
     	return $newLinesBecomeBreaks;
	}     
	
	public function getCustomerId() {
		$customerId = $this->getFailedTransactionData('customer_id'); 
		return $customerId;
	}
	
	public function getCustomerUrl() {
		$customerId = $this->getCustomerId();
		if($customerId === '0') {
			return "Customer was not logged in";
		}
		$url =  $this->getUrl('adminhtml/customer/edit/') . 'id/' . $customerId;
		return "<a href=" . $url . ">" . $customerId . "</a>";
	}
	
	public function getOrderId() {
		return $this->getFailedTransactionData('order_id');
	}

	public function getOrderNum() {
		return $this->getFailedTransactionData('order_num');
	}
	
	public function getOrderUrl() {
		$orderId = $this->getOrderId();
		$orderNum = $this->getOrderNum();
		if($orderId === "0") {
			return "No order information available";
		}
		$url = $this->getUrl('adminhtml/sales_order') . 'view/order_id/' . $orderId;
		return 	"<a href=" . $url . ">" . $orderNum . "</a>";
	}
}
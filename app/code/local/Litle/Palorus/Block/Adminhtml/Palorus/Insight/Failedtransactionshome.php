<?php
class Litle_Palorus_Block_Adminhtml_Palorus_Insight_Failedtransactionshome extends Mage_Adminhtml_Block_Widget_Form
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('litle/form/failedtransactionsview.phtml');
	}

	protected function _prepareLayout()
	{
		return parent::_prepareLayout();
	}

	private function getFailedTransactions(){
		return Mage::getModel('palorus/failedtransactions')->getCollection();
	}

	public function getFailedTransactionsTable()
	{
		$collection = $this->getFailedTransactions();
		$index=0;
		$table = array();
		foreach ($collection as $order){
			$row = $order->getData();
			$table[$index] = $row;
			$index = $index+1;
		}
		return $table;
	}

	public function getProductName($subscriptionRow)
	{
		$productName = $subscriptionRow['product_id'];
		$product = Mage::getModel('catalog/product')->load($productName);
		return $product->getName();
	}
	
	public function getRowUrl($row)
	{
		$failedTransactionId = $row['failed_transactions_id'];
		return $this->getUrl('palorus/adminhtml_myform/failedtransactionsview/', array('failed_transactions_id' => $failedTransactionId));
	}

}

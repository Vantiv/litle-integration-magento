<?php

class Litle_Palorus_Adminhtml_MyformController extends Mage_Adminhtml_Controller_Action
{
    public function activityAction()
    {
    	$this->_redirectUrl(Mage::helper('palorus')->getBaseUrl() . '/ui/reports/activity');
    }
    
    public function authorizationAction()
    {
    	$this->_redirectUrl(Mage::helper('palorus')->getBaseUrl() . '/ui/reports/authorization');
    }
    
    public function exchangeAction()
    {
    	$this->_redirectUrl(Mage::helper('palorus')->getBaseUrl() . '/ui/reports/exchange');
    }
    
    public function binlookupAction()
    {
    	$this->_redirectUrl(Mage::helper('palorus')->getBaseUrl() . '/ui/reports/binlookup');
    }
    
    public function sessionAction()
    {
    	$this->_redirectUrl(Mage::helper('palorus')->getBaseUrl() . '/ui/reports/operator/PresenterSessions.cgi?reportAction=LoadDefault');
    }
    
    public function settlementAction()
    {
    	$this->_redirectUrl(Mage::helper('palorus')->getBaseUrl() . '/ui/reports/settlement');
    }
    
    public function searchAction()
    {
    	$this->_redirectUrl(Mage::helper('palorus')->getBaseUrl() . '/ui/transactions/search');
    }
    
    public function summaryAction()
    {
    	$this->_redirectUrl(Mage::helper('palorus')->getBaseUrl() . '/ui/reports/transactions/summary');
    }
    
    public function dashboardauthorizationAction()
    {
    	$this->_redirectUrl(Mage::helper('palorus')->getBaseUrl() . '/ui/dashboards/authorization');
    }
    
    public function dashboardfrauddetectionAction()
    {
    	$this->_redirectUrl(Mage::helper('palorus')->getBaseUrl() . '/ui/dashboards/fraudDetection');
    }
    
    public function dashboardpostdepositfraudimpactAction()
    {
    	$this->_redirectUrl(Mage::helper('palorus')->getBaseUrl() . '/ui/dashboards/postDepositFraud');
    }
    
    public function chargebackSearchAction()
    {
    	$this->_redirectUrl(Mage::helper('palorus')->getBaseUrl() . '/ui/chargebacks/search');
    }
    
    public function chargebackReportAction()
    {
    	$this->_redirectUrl(Mage::helper('palorus')->getBaseUrl() . '/ui/reports/chargebacks/compliance');
    }    
    
    public function failedtransactionsAction()
    {
     	$this->loadLayout();
    	$block = $this->getLayout()->createBlock('palorus/adminhtml_palorus_insight_grid');
		$this->getLayout()->getBlock('content')->append($block);
    	$this->renderLayout();
    }
    
    public function massFailedTransactionsMarkActionTakenAction() {
    	$request = $this->getRequest();
    	$params = $request->getParams();
    	$failedTransactions = $params['failed_transactions_id'];
    	foreach($failedTransactions as $failedTransactionToDelete) {
    		$row = Mage::getModel("palorus/failedtransactions")->load($failedTransactionToDelete);
    		$row->setActive(false);
    		$row->save();
    	}
    	$this->failedtransactionsAction();
    }
    
    public function massFailedTransactionsMarkActionNotTakenAction() {
    	$request = $this->getRequest();
    	$params = $request->getParams();
    	$failedTransactions = $params['failed_transactions_id'];
    	foreach($failedTransactions as $failedTransactionToDelete) {
    		$row = Mage::getModel("palorus/failedtransactions")->load($failedTransactionToDelete);
    		$row->setActive(true);
    		$row->save();
    	}
    	$this->failedtransactionsAction();
    }
    
    
    public function failedtransactionsviewAction()
    {
    	$this->loadLayout();
    	$block = $this->getLayout()->createBlock('palorus/adminhtml_palorus_insight_failedtransactionsview');
    	$this->getLayout()->getBlock('content')->append($block);
    	$this->renderLayout();
    }
}
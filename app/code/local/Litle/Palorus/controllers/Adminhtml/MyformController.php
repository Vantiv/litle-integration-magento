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
    
    public function postAction()
    {
    	Mage::log("insightAction");
    	echo "hi from insight";
    }
    
    
}
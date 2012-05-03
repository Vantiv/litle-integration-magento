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
}
<?php

class Litle_Palorus_Adminhtml_MyformController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
    	$this->_redirectUrl('https://www.litle.com');
    }
    
    public function activityAction()
    {
    	$this->_redirectUrl('https://reports.litle.com/ui/reports/activity');
    }
    
    public function authorizationAction()
    {
    	$this->_redirectUrl('https://reports.litle.com/ui/reports/authorization');
    }
    
    public function exchangeAction()
    {
    	$this->_redirectUrl('https://reports.litle.com/ui/reports/exchange');
    }
    
    public function binlookupAction()
    {
    	$this->_redirectUrl('https://reports.litle.com/ui/reports/binlookup');
    }
    
    public function sessionAction()
    {
    	$this->_redirectUrl('https://reports.litle.com/ui/reports/operator/PresenterSessions.cgi?reportAction=LoadDefault');
    }
    
    public function settlementAction()
    {
    	$this->_redirectUrl('https://reports.litle.com/ui/reports/settlement');
    }
}
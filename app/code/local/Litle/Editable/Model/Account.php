<?php
class Litle_Editable_Model_Account
extends Mage_Core_Model_Abstract {
	private $customerId;
	private $orderId;
	private $affluence;
	
	public function __set($key,$val) {
		$this->$key=$val;
	}
	public function __get($key) {
		return $this->$key;
	}
	
//  	public function setCustomerId($customer_id) {
//  		this->$customerId = $customer_id;
//  	}
 	
//  	public function getCustomerId() {
//  		return $customerId;
//  	}
 	
//  	public function setOrderId($order_id) {
//  		this->$orderId = $order_id;
//  	}
 	
//  	public function getOrderId() {
//  		return $orderId;
//  	}
 	
//  	public function setAffluence($affluence_param) {
//  		this->$affluence = $affluence_param;
//  	}
 	
//  	public function getAffluence() {
//  		return $affluence;
//  	}
	
	public function saveIt() {
		Mage::log("Gonna get a connection now");
		$connection = Mage::getSingleton('core/resource')->getConnection('editable_write');
		Mage::log("have connection");
		$connection->beginTransaction();
		Mage::log("have transaction");
		$fields = array();
		$fields['customer_id'] = $this->customerId;
		$fields['order_id'] = $this->orderId;
		$fields['affluence'] = $this->affluence;
		Mage::log("have fields");
		try {
			//$this->_beforeSave();			
				$connection->insert('editable_account', $fields);
				Mage::log("Insert done");
				//$this->rewardpointsAccountId = $connection->lastInsertId('editable_account');
			$connection->commit();
			Mage::log("Commited");
			//$this->_afterSave();
		}
		catch (Exception $e) {
			Mage::log("Exception");	
			Mage::log($e->getMessage());		
			$connection->rollBack();
			throw $e;
		}
		Mage::log("returning");
		return $this;
	}
	//add and subtract points methods
}
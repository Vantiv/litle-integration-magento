<?php
class Litle_Editable_Model_Account
extends Mage_Core_Model_Abstract {
	protected $customerId = -1;
	protected $storeId = -1;
	protected $pointsCurrent = NULL;
	protected $pointsReceived = NULL;
	protected $pointsSpent = NULL;
	//public setters and getters for every attribute
	//save and load methods
	
// 	public function setCustomerId($customer_id) {
// 		this->$customerId = $customer_id;
// 	}
	
// 	public function setStoreId($store_id) {
// 		this->$storeId = $store_id;
// 	}
	
	public function saveIt() {
		Mage::log("Gonna get a connection now");
		$connection = Mage::getSingleton('core/resource')->getConnection('editable_write');
		Mage::log("have connection");
		$connection->beginTransaction();
		Mage::log("have transaction");
		$fields = array();
// 		$fields[’customer_id’] = $this->customerId;
// 		$fields[’store_id’] = $this->storeId;
		$fields['customer_id'] = 1;
		$fields['store_id'] = 1;
		Mage::log("have fields");
		//$fields[’points_current’] = $this->pointsCurrent;
		//$fields[’points_received’] = $this->pointsReceived;
		//$fields[’points_spent’] = $this->pointsSpent;
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
			$connection->rollBack();
			throw $e;
		}
		Mage::log("returning");
		return $this;
	}
	//add and subtract points methods
}
<?php

$installer = $this;
Mage::log("About to start upgrade from 8.13.3 to 8.14.0", null, "litle_install.log");
$installer->startSetup();

$sql = "
	CREATE TABLE IF NOT EXISTS `{$installer->getTable('palorus/failedtransactions')}` (
	  `failed_transactions_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `customer_id` int(10) unsigned,
	  `order_id` int(10) unsigned,
	  `message` varchar(255),
	  `full_xml` varchar(512),
	  `litle_txn_id` varchar(25),
	  `active` bool,
	  `transaction_timestamp` timestamp,
	  `order_num` int(10) unsigned,
	  PRIMARY KEY (`failed_transactions_id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
";
Mage::log("SQL: " . $sql, null, "litle_install.log");
$installer->run($sql);
Mage::log("Created table litle_failed_transactions", null, "litle_install.log");

Mage::log("About to end upgrade from 8.13.3 to 8.14.0", null, "litle_install.log");
$installer->endSetup();

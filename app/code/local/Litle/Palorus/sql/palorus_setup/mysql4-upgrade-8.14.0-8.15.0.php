<?php

$installer = $this;
Mage::log("About to start upgrade from 8.14.0 to 8.15.0", null, "litle_install.log");
$installer->startSetup();

$sql = "
	CREATE TABLE IF NOT EXISTS `{$installer->getTable('palorus/avscid')}` (
	  `avs_cid_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `order_id` int(10) unsigned,
	  `avs_response` varchar(3),
	  `cid_response` varchar(3),
	  PRIMARY KEY (`avs_cid_id`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
";
Mage::log("SQL: " . $sql, null, "litle_install.log");
$installer->run($sql);
Mage::log("Created table litle_avs_cid", null, "litle_install.log");

Mage::log("About to end upgrade from 8.14.0 to 8.15.0", null, "litle_install.log");
$installer->endSetup();

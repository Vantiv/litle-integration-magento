<?php

$installer = $this;
Mage::log("About to start upgrade from 8.13.2 to 8.13.3", null, "litle_install.log");
$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales/quote_payment'), 'litle_vault_id', 'int(10)');
Mage::log("Added column litle_vault_id to quote_payment", null, "litle_install.log");
$installer->getConnection()->addColumn($installer->getTable('sales/order_payment'), 'litle_vault_id', 'int(10)');
Mage::log("Added column litle_vault_id to order_payment", null, "litle_install.log");

$sql = "
	CREATE TABLE IF NOT EXISTS `_{$installer->getTable('palorus/vault')}_tmp` (
	  `vault_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
	  `customer_id` int(10) unsigned NOT NULL DEFAULT '0',
	  `last4` varchar(4) DEFAULT NULL,
	  `token` varchar(25) DEFAULT NULL,
	  `type` varchar(2) DEFAULT NULL,
	  `bin` varchar(6) DEFAULT NULL,
	  `expiration_month` tinyint(2) DEFAULT NULL,
	  `expiration_year` smallint(4) DEFAULT NULL,
	  `updated` datetime DEFAULT NULL,
	  `created` datetime DEFAULT NULL,
	  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
	  PRIMARY KEY (`vault_id`),
	  UNIQUE KEY `customer_token` (`customer_id`, `token`)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
";
Mage::log("SQL: " . $sql, null, "litle_install.log");
$installer->run($sql);
Mage::log("Create table litle_vault_tmp", null, "litle_install.log");

$sql = "
	INSERT INTO `_{$installer->getTable('palorus/vault')}_tmp`
		(vault_id,customer_id,last4,token,`type`,`bin`,updated,created)
		SELECT
		v.vault_id,customer_id,last4,v.token,`type`,`bin`,NOW(), NOW()
		FROM `{$installer->getTable('palorus/vault')}` v
		JOIN (
			SELECT MAX(vault_id) vault_id,token FROM `{$installer->getTable('palorus/vault')}` GROUP BY token
		) v2 ON v.vault_id = v2.vault_id;
";
Mage::log("SQL: " . $sql, null, "litle_install.log");
$installer->run($sql);
Mage::log("Copied contents of litle_vault to litle_vault_tmp", null, "litle_install.log");

$sql = "TRUNCATE TABLE `{$installer->getTable('palorus/vault')}`";
Mage::log("SQL: " . $sql, null, "litle_install.log");
$installer->run($sql);
Mage::log("Truncated litle_vault", null, "litle_install.log");

$sql = "
	ALTER TABLE  `{$installer->getTable('palorus/vault')}`
		ADD `expiration_month` TINYINT( 2 ) NULL DEFAULT NULL,
		ADD `expiration_year` SMALLINT( 4 ) NULL DEFAULT NULL,
		ADD `updated` DATETIME NULL DEFAULT NULL,
		ADD `created` DATETIME NULL DEFAULT NULL,
		ADD `is_visible` TINYINT( 1 ) NOT NULL DEFAULT  '1',
		ADD UNIQUE (`customer_id`, `token`)
";
Mage::log("SQL: " . $sql, null, "litle_install.log");
$installer->run($sql);
Mage::log("Added expiration_month, expiration_year, updated, created, is_visible to litle_vault", null, "litle_install.log");

$sql = "INSERT INTO `{$installer->getTable('palorus/vault')}` SELECT * FROM `_{$installer->getTable('palorus/vault')}_tmp`;";
Mage::log("SQL: " . $sql, null, "litle_install.log");
$installer->run($sql);
Mage::log("Loaded everything from litle_vault_tmp back into litle_vault", null, "litle_install.log");

$sql = "
	UPDATE `{$installer->getTable('palorus/vault')}` v
		LEFT JOIN `{$installer->getTable('sales/order')}` sfo ON sfo.entity_id=v.order_id
		SET v.order_id = sfo.increment_id;
";
Mage::log("SQL: " . $sql, null, "litle_install.log");
$installer->run($sql);
Mage::log("Updated litle_vault setting the order_id", null, "litle_install.log");

$sql = "DROP TABLE `_{$installer->getTable('palorus/vault')}_tmp`;";
Mage::log("SQL: " . $sql, null, "litle_install.log");
$installer->run($sql);
Mage::log("Dropped temporary litle_vault_tmp table", null, "litle_install.log");

Mage::log("About to end upgrade from 8.13.2 to 8.13.3", null, "litle_install.log");
$installer->endSetup();

<?php

$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('sales/quote_payment'), 'litle_vault_id', 'int(10)');
$installer->getConnection()->addColumn($installer->getTable('sales/order_payment'), 'litle_vault_id', 'int(10)');

$installer->run("
ALTER TABLE  `{$installer->getTable('palorus/vault')}`
	ADD `expiration_month` TINYINT( 2 ) NULL DEFAULT NULL,
	ADD `expiration_year` SMALLINT( 4 ) NULL DEFAULT NULL,
	ADD `updated` DATETIME NULL DEFAULT NULL,
	ADD `created` DATETIME NULL DEFAULT NULL,
	ADD `order_number` INT UNSIGNED NULL DEFAULT NULL AFTER  `order_id`,
	ADD `is_visible` TINYINT( 1 ) NOT NULL DEFAULT  '1',
	ADD UNIQUE (`token`);
");

$installer->endSetup();

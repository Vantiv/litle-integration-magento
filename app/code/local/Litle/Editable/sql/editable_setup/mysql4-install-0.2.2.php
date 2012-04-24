<?php
     
    $installer = $this;
     
    $installer->startSetup();
     
    $installer->run("
     
DROP TABLE IF EXISTS {$this->getTable('editable_account')};
CREATE TABLE {$this->getTable('editable_account')} (
'editable_account_id' integer(10) unsigned NOT NULL auto_increment,
'customer_id' integer(10) unsigned NOT NULL default '0',
'store_id' smallint(5) unsigned NOT NULL default '0',
'points_current' integer(10) unsigned NULL default '0',
'points_received' integer(10) unsigned NULL default '0',
'points_spent' integer(10) unsigned NULL default '0',
PRIMARY KEY ('editable_account_id'),
KEY 'FK_catalog_category_ENTITY_STORE' ('store_id'),
KEY 'customer_idx' ('customer_id')
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Reward points for an account';
");
     
    $installer->endSetup();
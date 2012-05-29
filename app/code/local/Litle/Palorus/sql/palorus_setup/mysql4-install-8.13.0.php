<?php
     
    $installer = $this;
     
    $installer->startSetup();

    $installer->run("
    DROP TABLE IF EXISTS {$installer->getTable('palorus/insight')};
    ");
    
    $installer->run("
CREATE TABLE {$installer->getTable('palorus/insight')} (
customer_insight_id integer(10) unsigned NOT NULL auto_increment,
customer_id integer(10) unsigned NOT NULL default 0,
order_number integer(10) unsigned NOT NULL default 0,
order_id integer(10) unsigned NOT NULL default 0,
last varchar(20) NULL,
order_amount varchar(20) NULL,
affluence varchar(15) NULL,
issuing_country varchar(20) NULL,
prepaid_card_type varchar(20) NULL,
funding_source varchar(20) NULL,
available_balance varchar(20) NULL,
reloadable varchar(20) NULL,
PRIMARY KEY (customer_insight_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Litle customer insight for an account';
");
    
    $installer->run("
        DROP TABLE IF EXISTS {$installer->getTable('palorus/vault')};
    ");
    
    $installer->run("
CREATE TABLE {$installer->getTable('palorus/vault')} (
vault_id integer(10) unsigned NOT NULL auto_increment,
order_id integer(10) unsigned NOT NULL default 0,
customer_id integer(10) unsigned NOT NULL default 0,
last4 varchar(4) NULL,
token varchar(25) NULL,
type varchar(2) NULL,
bin varchar(6) NULL,
PRIMARY KEY (vault_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Litle vaulted credit cards for an account';
    ");
    
     
    $installer->endSetup();
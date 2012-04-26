<?php
     
    $installer = $this;
     
    $installer->startSetup();
     
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
     
    $installer->endSetup();
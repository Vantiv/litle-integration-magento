<?php
     
    $installer = $this;
     
    $installer->startSetup();
     
    $installer->run("
     
CREATE TABLE {$installer->getTable('palorus/insight')} (
customer_insight_id integer(10) unsigned NOT NULL auto_increment,
customer_id integer(10) unsigned NOT NULL default 0,
order_id integer(10) unsigned NOT NULL default 0,
affluence varchar(15) NULL,
last varchar(20) NULL,
prepaid varchar(20) NULL,
reloadable varchar(20) NULL,
available_balance integer(10) NULL,
issuing_country varchar(20) NULL, 
PRIMARY KEY (customer_insight_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Litle customer insight for an account';
");
     
    $installer->endSetup();
<?php
     
    $installer = $this;
     
    $installer->startSetup();
     
    $installer->run("
     
DROP TABLE IF EXISTS customer_insight;
CREATE TABLE customer_insight (
'customer_insight_id' integer(10) unsigned NOT NULL auto_increment,
'customer_id' integer(10) unsigned NOT NULL default '0',
'order_id' integer(10) unsigned NOT NULL default '0',
'affluence' varchar(15) NULL,
PRIMARY KEY ('customer_insight_id')
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Litle customer insight for an account';
");
     
    $installer->endSetup();
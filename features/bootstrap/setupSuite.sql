drop table customer_insight;
drop table vault;
delete from core_resource where code = 'palorus_setup';
delete from core_config_data where path like 'payment/CreditCard/%';
delete from core_config_data where path like 'payment/LEcheck/%';

INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/active','1');
INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/title','Credit Card');
INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/user','USER');
INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/password','PASSWORD');
INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/merchant_id','(''USD''=>''101'')');
INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/order_status','processing');
INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/payment_action','authorize');
INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/url','https://www.testlitle.com/sandbox/communicator/online');
INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/paypage_enable','0');
INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/paypage_url',null);
INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/paypage_id',null);
INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/timeout',null);
INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/LEcheck/active','0');
INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/LEcheck/title',null);
INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/LEcheck/payment_action','authorize');
INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/LEcheck/order_status',null);
INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/proxy','smoothproxy:8080');


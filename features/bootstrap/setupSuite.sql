drop table customer_insight;
drop table vault;
delete from core_resource where code = 'palorus_setup';
delete from core_config_data where path like 'payment/CreditCard/%';
delete from core_config_data where path like 'payment/LEcheck/%';

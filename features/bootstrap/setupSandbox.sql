
update core_config_data set value='http://l-gdake-t5500:8081/sandbox/communicator/online' where path='payment/CreditCard/url';
update core_config_data set value='http://l-gdake-t5500:8081/sandbox/communicator/online' where path='payment/LEcheck/url';

update core_config_data set value=NULL where path='payment/CreditCard/proxy';
update core_config_data set value=NULL where path='payment/LEcheck/proxy';

update core_config_data set value='JENKINS' where path='payment/CreditCard/user';
update core_config_data set value='JENKINS' where path='payment/LEcheck/user';
update core_config_data set value='CustomerInformation' where path='payment/CreditCard/password';
update core_config_data set value='CustomerInformation' where path='payment/LEcheck/password';
update core_config_data set value='("USD"=>"101","GBP"=>"102")' where path='payment/CreditCard/merchant_id';
update core_config_data set value='01602' where path='payment/LEcheck/merchant_id';
update core_config_data set value='authorize' where path='payment/CreditCard/payment_action';
update core_config_data set value=0 where path='payment/CreditCard/paypage_enable';

delete from sales_flat_order;
delete from sales_flat_quote;

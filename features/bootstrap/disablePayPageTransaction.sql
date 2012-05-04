
update core_config_data set value='http://l-gdake-t5500:8081/sandbox/communicator/online' where path='payment/CreditCard/url';
update core_config_data set value='http://l-gdake-t5500:8081/sandbox/communicator/online' where path='payment/LEcheck/url';

update core_config_data set value='' where path='payment/CreditCard/proxy';
update core_config_data set value='' where path='payment/LEcheck/proxy';

update core_config_data set value='1' where path='payment/LEcheck/active';
update core_config_data set value='1' where path='payment/CreditCard/active';

update core_config_data set value='JENKINS' where path='payment/CreditCard/user';
update core_config_data set value='JENKINS' where path='payment/LEcheck/user';
update core_config_data set value='PayPageTransactions' where path='payment/CreditCard/password';
update core_config_data set value='PayPageTransactions' where path='payment/LEcheck/password';
update core_config_data set value='101' where path='payment/CreditCard/merchant_id';
update core_config_data set value='101' where path='payment/LEcheck/merchant_id';

update core_config_data set value='0' where path='payment/CreditCard/paypage_enable';
update core_config_data set value='http://l-gdake-t5500:2184/' where path='payment/CreditCard/paypage_url';
update core_config_data set value='a2y4o6m8k0' where path='payment/CreditCard/paypage_id';

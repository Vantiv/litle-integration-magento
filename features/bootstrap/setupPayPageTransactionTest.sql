
update core_config_data set value='https://www.testlitle.com/sandbox/communicator/online' where path='payment/CreditCard/url';
update core_config_data set value='https://www.testlitle.com/sandbox/communicator/online' where path='payment/LEcheck/url';

update core_config_data set value='smoothproxy:8080' where path='payment/CreditCard/proxy';
update core_config_data set value='smoothproxy:8080' where path='payment/LEcheck/proxy';

update core_config_data set value='JENKINS' where path='payment/CreditCard/user';
update core_config_data set value='JENKINS' where path='payment/LEcheck/user';
update core_config_data set value='CustomerInformation' where path='payment/CreditCard/password';
update core_config_data set value='CustomerInformation' where path='payment/LEcheck/password';
update core_config_data set value='01602' where path='payment/CreditCard/merchant_id';
update core_config_data set value='01602' where path='payment/LEcheck/merchant_id';
update core_config_data set value='authorize' where path='payment/CreditCard/payment_action';
update core_config_data set value='1' where path='payment/CreditCard/paypage_enabled';
update core_config_data set value='precert01' where path='payment/CreditCard/paypage_url';
update core_config_data set value='a2y4o6m8k0' where path='payment/CreditCard/paypage_id';

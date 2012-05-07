update core_config_data set value='http://l-gdake-t5500:8081/sandbox/communicator/online' where path='payment/CreditCard/url';

update core_config_data set value='' where path='payment/CreditCard/proxy';

update core_config_data set value='1' where path='payment/LEcheck/active';
update core_config_data set value='1' where path='payment/CreditCard/active';

update core_config_data set value='JENKINS' where path='payment/CreditCard/user';
update core_config_data set value='PayPageTransactions' where path='payment/CreditCard/password';
update core_config_data set value='("USD"=>"101","GBP"=>"102")' where path='payment/CreditCard/merchant_id';
update core_config_data set value='AE,DC,VI,MC,DI,JCB' where path='payment/CreditCard/cctypes';
update core_config_data set value='Litle ECheck' where path='payment/LEcheck/title';
update core_config_data set value='Litle Credit Card' where path='payment/CreditCard/title';

delete from sales_flat_order;
delete from sales_flat_quote;
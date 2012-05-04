
update core_config_data set value='http://l-gdake-t5500:2180/vap/communicator/online' where path='payment/CreditCard/url';

update core_config_data set value=NULL where path='payment/CreditCard/proxy';

update core_config_data set value='ECHK2XML' where path='payment/CreditCard/user';
update core_config_data set value='password' where path='payment/CreditCard/password';
update core_config_data set value='("USD"=>"101","GBP"=>"102")' where path='payment/CreditCard/merchant_id';
update core_config_data set value=0 where path='payment/CreditCard/paypage_enable';

delete from sales_flat_quote
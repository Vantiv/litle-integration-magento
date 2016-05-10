## Description
This extension will allow you to accept payments through Litle & Co. on Magento.

## Installation
*To install Magento, follow directions on the following page:
[Magento Installation Guide](http://www.magentocommerce.com/wiki/1_-_installation_and_configuration/magento_installation_guide)
* Download Litle_Payments-[version number].tgz file
* Login to Admin panel in Magento
* Open Magento Connect Manager (System > Magento Connect > Magento Connect Manager)
* Under "Direct package file upload", browse to Litle_Payments-[version number].tgz file
* Click on "Upload" to install

## Setup
Login to Admin panel in Magento - Navigate to System > Configuration
### Enable Litle Payments

* Go to Advanced > Advanced
* Under "Disable Modules Output", set Litle_CreditCard to "Enable" and Litle_LEcheck to "Enable"

### Setup Litle Payments
* Go to Sales > Payment Methods

* Expand Litle - Credit Card. Set the field values as:
    
    Enabled: Yes

    Title: Heading you would like your customers to see. Typically set to Credit Card
    
    User: Litle User Name
    
    Password: Litle password
    
    Merchant ID*: Litle Merchant ID
    
    Report Group: Default Report Group
    
    New Order Status: Processing
    
    Payment Action: You may choose "Authorize Only", or "Authorize and Capture". If you choose "Authorize Only", you will have to manually process the Captures later.
    
    HTTP URL*: If performing preliminary testing, you may select Sandbox. If you are in process of setting up an account with Litle, then you may select Postlive, Prelive1, or Production depending on which step you are at.
    
    HTTP Proxy: If you need to use a proxy, you need to enter it here.
    
    HTTP Timeout: Recommended timeout is 65
        
* Expand Litle - Echeck.  The Echeck configuration uses the same configuration as above, except for:
    Title: Typically set to ECheck

    *Payment Action: You may choose "Verification", or "Sale". Selecting verification will only verify the account upon customer checkout and a sale transaction can be done at a later time. Choosing sale will do both a verification and a sale transaction with the payment information upon checkout. 

    *Account Types: Select account types from which you would like to accept payments. You may select multiple types by holding Ctrl.
    
* Click on "Save Config".  *Upon clicking "Save Config", Magento will try to connect to Litle & Co servers to check connectivity. In-case the connect fails, you will be shown an error message.  * In addition hitting the 'Save Config' file will check to see that the merchantId is in the proper format. The merchantId should be of the form ('currency'=> merchantId), this allows for multiple currecncy support. Note: USD must be present in the merchantId.If you see the error message and need help, please contact Litle SDK team at: sdksupport@litle.com.

NOTE: You do not need a valid username and/or password while connecting to Sandbox.  You may enter any fake values.

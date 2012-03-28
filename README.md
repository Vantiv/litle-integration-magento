# Litle & Co. Magento Extension

## Description
This extension will allow you to accept payments through Litle & Co. on Magento.

## Installation:
* Download Litle_Payments-[version number].tgz file
* Login to Admin panel in Magento
** Open Magento Connect Manager (System > Magento Connect > Magento Connect Manager)
** Under "Direct package file upload", browse to Litle_Payments-[version number].tgz file
** Click on "Upload" to install

## Setup:
* Login to Admin panel in Magento - Navigate to System > Configuration
** Enable Litle Payments
*** Go to Advanced > Advanced
*** Under "Disable Modules Output", set Litle_CreditCard to "Enable" and Litle_LEcheck to "Enable"
** Setup Litle Payments
*** Go to Sales > Payment Methods
*** Expand Litle - Credit Card
*** Set the field values as:
**** Enabled: Yes
**** Title: Heading you would like your customers to see. Typically set to Credit Card
**** User: Litle User Name
**** Password: Litle password
**** Merchant ID: Litle Merchant ID
**** Report Group: Default Report Group
**** New Order Status: Processing
**** Payment Action: You may choose "Authorize Only", or "Authorize and Capture". If you choose "Authorize Only", you will have to manually process the Captures later.
**** HTTP URL: If performing preliminary testing, you may select Sandbox. If you are in process of setting up an account with Litle, then you may select Pre-Cert, Cert, or Production depending on which step you are at.
**** HTTP Proxy: If you need to use a proxy, you need to enter it here.
**** HTTP Timeout: Recommended timeout is 65
*** Expand Litle - Echeck
**** Set the field values same as above, except for:
***** Title: Typically set to ECheck
***** Account Types: Select account types from which you would like to accept payments. You may select multiple types by holding Ctrl.
***** Click on "Save Config"
*Upon clicking "Save Config", Magento will try to connect to Litle & Co servers to check connectivity. In-case the connect fails, you will be shown an error message. If you see the error message and need help, please contact Litle SDK team at: sdksupport@litle.com
NOTE: You do not need a valid username and/or password while connecting to Sandbox.
      You may enter any fake values.

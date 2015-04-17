package com.litle.magento.selenium;

import org.junit.Before;
import org.junit.Test;

public class BackendLPaypalTransactionTests extends BaseTestCase {
    @Before
    public void setup() throws Exception {
        iAmDoingLPaypalTransaction();
    }
    
    @Test
    public void doASuccessfulAuthCheckoutAndThenReverseTheAuth() throws Exception {
        iAmDoingLPaypalAuth();

        iAmLoggedInAsWithThePassword("abc@gmail.com", "password");
        iHaveInMyCart("vault");
        iCheckOutInCartWithLPaypal("sdksupport-buyer@litle.com", "vantiv2015");
        iLogOutAsUser();

        iAmLoggedInAsAnAdministrator();
        iView("Sales","Orders");
        iClickOnTheTopRowInOrders();
        iPressAuthReversal("The payment has been voided.");
        iLogOutAsAdministrator();
    }
    
    @Test
    public void doASuccessfulAuthCheckoutAndThenCancelTheAuth() throws Exception {
        iAmDoingLPaypalAuth();

        iAmLoggedInAsWithThePassword("abc@gmail.com", "password");
        iHaveInMyCart("vault");
        iCheckOutInCartWithLPaypal("sdksupport-buyer@litle.com", "vantiv2015");
        iLogOutAsUser();

        iAmLoggedInAsAnAdministrator();
        iView("Sales","Orders");
        iClickOnTheTopRowInOrders();
        iPressCancel("The order has been cancelled.");
        iLogOutAsAdministrator();
    }
    
    @Test
    public void doASuccessfulRefundAndThenVoidTheRefund() throws Exception {
        iAmDoingLPaypalAuth();

        iAmLoggedInAsWithThePassword("abc@gmail.com", "password");
        iHaveInMyCart("vault");
        iCheckOutInCartWithLPaypal("sdksupport-buyer@litle.com", "vantiv2015");
        iLogOutAsUser();

        iAmLoggedInAsAnAdministrator();
        iView("Sales","Orders");
        iClickOnTheTopRowInOrders();
        iPressInvoice();
        iPressSubmitInvoice("The invoice has been created.", null);
        iClickOnInvoices();
        iClickOnTheTopRowInInvoices();
        iPressCreditMemo();
        iPressRefund("The credit memo has been created.");
        iPressVoidRefund("The payment has been voided.");
        iLogOutAsAdministrator();
    }
    
    @Test
    public void doAVoidCaptureAndThenCaptureAgain() throws Exception {
        iAmDoingLPaypalAuth();

        iAmLoggedInAsWithThePassword("abc@gmail.com", "password");
        iHaveInMyCart("vault");
        iCheckOutInCartWithLPaypal("sdksupport-buyer@litle.com", "vantiv2015");
        iLogOutAsUser();

        iAmLoggedInAsAnAdministrator();
        iView("Sales","Orders");
        iClickOnTheTopRowInOrders();
        iPressInvoice();
        iPressSubmitInvoice("The invoice has been created.",null);
        iPressVoidCapture("The payment has been voided.");
        iPressInvoice();
        iPressSubmitInvoice("The invoice has been created.","Captured amount of $6.99 online");
        iLogOutAsAdministrator();
    }

//    @Test
//    public void attemptAFailedVoidCapture() throws Exception {
//        iAmDoingLitleAuth();
//
//        iAmLoggedInAsWithThePassword("abc@gmail.com","password");
//        iHaveInMyCart("vault");
//        iCheckOutWith("American Express","346854278192102");
//        iLogOutAsUser();
//
//        iAmLoggedInAsAnAdministrator();
//        iView("Sales","Orders");
//        iClickOnTheTopRowInOrders();
//        iPressInvoice();
//        iPressSubmitInvoice("The invoice has been created.",null);
//        iPressVoidCapture("Transaction Not Voided - Already Settled. This transaction cannot be voided; it has already been delivered to the card networks. You may want to try a refund instead.For your reference, the transaction id is \\d+");
//        iLogOutAsAdministrator();
//    }
    
}

package com.litle.magento.selenium;

import org.junit.Before;
import org.junit.Test;
import org.openqa.selenium.By;

public class NonPayPageTransactionsTests extends BaseTestCase {

    @Before
    public void setUp() throws Exception {
        iAmDoingCCOrEcheckTransaction();
        iAmDoingNonPaypageTransaction();
    }

	@Test
	public void doASuccessfulAuthAndThenCaptureTheAuth() throws Exception {
	    iAmDoingLitleAuth();
	    iAmLoggedInAsWithThePassword("gdake@litle.com", "password");
	    iHaveInMyCart("vault");
	    iCheckOutWith("Visa", "4100000000000001");
	    iLogOutAsUser();

	    iAmLoggedInAsAnAdministrator();
	    iView("Sales", "Orders");
	    iClickOnTheTopRowInOrders();
	    iPressInvoice();
	    iSelectNameFromSelect("Capture Online", "invoice[capture_case]");
	    iPressSubmitInvoice("The invoice has been created.", null);
	    iLogOutAsAdministrator();
	}

	@Test
	public void doAnUnsuccessfulCheckout() throws Exception {
	    iAmLoggedInAsWithThePassword("gdake@litle.com", "password");
	    iHaveInMyCart("vault");
	    iFailCheckOutWith("Visa", "4137307201736110", "The order was not approved.  Please try again later or contact us.  For your reference, the transaction id is");
	    iLogOutAsUser();
	}

	@Test
	public void backendAuthCheckoutCapture() throws Exception {
	    iAmDoingLitleAuth();
	    iAmLoggedInAsAnAdministrator();
	    iView("Sales", "Orders");
	    iPressCreateNewOrder();
	    iClickOnTheCustomerWithEmail("gdake@litle.com");
        iClickAddProducts();
        iAddTheTopRowInProductsToTheOrder();

        waitFor(By.id("p_method_creditcard"));
        driver.findElement(By.id("p_method_creditcard")).click();
        waitFor(By.id("creditcard_cc_type"));
        iSelectFromSelect("Visa", "creditcard_cc_type");
        driver.findElement(By.id("creditcard_cc_number")).sendKeys("4100000000000001");
        iSelectFromSelect("09 - September", "creditcard_expiration");
        iSelectFromSelect("2015", "creditcard_expiration_yr");
        driver.findElement(By.id("creditcard_cc_cid")).sendKeys("123");
        driver.findElement(By.id("order-comment")).click();

        //And I configure shipping method
        waitFor(By.id("order-shipping-method-summary"));
        driver.findElement(By.id("order-shipping-method-summary")).click();
        waitForIdVisible("s_method_flatrate_flatrate");
        driver.findElement(By.id("s_method_flatrate_flatrate")).click();
        driver.findElement(By.id("order-comment")).click();

        iPressSubmitOrder();

        iView("Sales", "Orders");
        iClickOnTheTopRowInOrders();
        iPressInvoice();
        iSelectNameFromSelect("Capture Online", "invoice[capture_case]");
        iPressSubmitInvoice("The invoice has been created.", null);
        iLogOutAsAdministrator();
	}

	@Test
	public void doASucessfullSale() throws Exception {
        iAmDoingLitleSale();
        iAmLoggedInAsWithThePassword("gdake@litle.com", "password");
        iHaveInMyCart("vault");
        iCheckOutWith("Visa", "4100000000000001");
        iLogOutAsUser();
	}
}

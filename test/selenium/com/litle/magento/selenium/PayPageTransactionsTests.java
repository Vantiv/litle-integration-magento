package com.litle.magento.selenium;


import org.junit.Before;
import org.junit.Test;

public class PayPageTransactionsTests extends BaseTestCase {

    @Before
    public void setUp() throws Exception {
        iAmDoingCCOrEcheckTransaction();
        iAmDoingPaypageTransaction();
    }
@Test public void testFoo() {}
/*
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
*/
/*
	@Test
	public void backendPaypageAuthCheckoutThenAttemptCapture() throws Exception {
	    iAmDoingLitleAuth();
	    iAmLoggedInAsAnAdministrator();
	    iView("Sales", "Orders");
	    iPressCreateNewOrder();
        iClickOnTheCustomerWithEmail("gdake@litle.com");
        iClickAddProducts();
        iAddTheTopRowInProductsToTheOrder();

        waitFor(By.id("p_method_creditcard"));
        driver.findElement(By.id("p_method_creditcard")).click();
        waitFor(By.id("payment_form_creditcard"));
        WebElement e = driver.findElement(By.id("payment_form_creditcard"));
        String linkText = e.getText();
        assertEquals("Litle Virtual Terminal", linkText);
        System.out.println(linkText);
        String url = e.findElement(By.tagName("a")).getAttribute("href");
        System.out.println(url);
        assertEquals("https://reports.litle.com/ui/vt", url);

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

        iPressSubmitInvoice("This order was placed using Litle Virtual Terminal. Please process the capture by logging into Litle Virtual Terminal (https://reports.litle.com).", null);
        iLogOutAsAdministrator();
	}
//TODO The sandbox PayPage isn't equipped to deal with failures
//    @Test
//    public void doAnUnsuccessfulCheckout() throws Exception {
//        iAmLoggedInAsWithThePassword("gdake@litle.com", "password");
//        iHaveInMyCart("vault");
//        iFailCheckOutWith("Visa", "4137307201736110", "The order was not approved.  Please try again later or contact us.  For your reference, the transaction id is");
//        iLogOutAsUser();
//    }
//
*/
    @Test
    public void doASucessfullSale() throws Exception {
        iAmDoingLitleSale();
        iAmLoggedInAsWithThePassword("gdake@litle.com", "password");
        iHaveInMyCart("vault");
        iCheckOutWith("Visa", "4100000000000001");
        iLogOutAsUser();
    }
}

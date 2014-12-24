package com.litle.magento.selenium;



import org.junit.Before;
import org.junit.Test;

public class EcheckTransactionsTests extends BaseTestCase {

    @Before
    public void background() throws Exception {
        iAmDoingCCOrEcheckTransaction();
    }

    @Test
    public void doAVerifiyAndThenSale() throws Exception {
        iAmLoggedInAsWithThePassword("gdake@litle.com", "password");
        iHaveInMyCart("vault");
        iCheckOutWithEcheck("123456000", "123456000", "Checking");
        iLogOutAsUser();

        iAmLoggedInAsAnAdministrator();
        iView("Sales", "Orders");
        iClickOnTheTopRowInOrders();
        iPressInvoice();
        iPressSubmitInvoice(null, null);
        iLogOutAsAdministrator();
    }
/*
    @Test
    public void testBackendECheckVerifyThenAttemptSale() throws Exception {
        iAmLoggedInAsAnAdministrator();
        iView("Sales", "Orders");
        iPressCreateNewOrder();
        iClickOnTheCustomerWithEmail("gdake@litle.com");
        iClickAddProducts();
        iAddTheTopRowInProductsToTheOrder();

        //And I choose "Echeck"
        waitFor(By.id("p_method_lecheck"));
        driver.findElement(By.id("p_method_lecheck")).click();
        //And I enter a routing number
        waitForIdVisible("lecheck_echeck_routing_number");
        WebElement e = driver.findElement(By.id("lecheck_echeck_routing_number"));
        e.clear();
        e.sendKeys("123456000");
        //And I enter a bank account number
        e = driver.findElement(By.id("lecheck_echeck_bank_acct_num"));
        e.clear();
        e.sendKeys("123456000");
        //And I select Checking
        iSelectFromSelect("Checking", "lecheck_echeck_account_type");

        //And I configure shipping method
        waitFor(By.id("order-shipping-method-summary"));
        driver.findElement(By.id("order-shipping-method-summary")).click();
        waitForIdVisible("s_method_flatrate_flatrate");
        driver.findElement(By.id("s_method_flatrate_flatrate")).click();

        //And I choose "Echeck"
        waitFor(By.id("p_method_lecheck"));
        driver.findElement(By.id("p_method_lecheck")).click();
        //And I enter a routing number
        waitForIdVisible("lecheck_echeck_routing_number");
        e = driver.findElement(By.id("lecheck_echeck_routing_number"));
        e.clear();
        e.sendKeys("123456000");
        //And I enter a bank account number
        e = driver.findElement(By.id("lecheck_echeck_bank_acct_num"));
        e.clear();
        e.sendKeys("123456000");
        //And I select Checking
        iSelectFromSelect("Checking", "lecheck_echeck_account_type");

        //And I press Submit order
        //waitFor(By.id("order-totals-bottom"));
        //driver.findElement(By.id("order-totals-bottom")).findElement(By.tagName("button")).click();
        iPressSubmitOrder();
        iView("Sales", "Orders");

        iClickOnTheTopRowInOrders();
        iPressInvoice();
        iPressSubmitInvoice("The invoice has been created.", "Captured amount of $6.99 online. Transaction ID:");
        iLogOutAsAdministrator();
    }
*/
}

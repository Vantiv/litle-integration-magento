package com.litle.magento.selenium;

import static org.junit.Assert.fail;

import org.junit.Before;
import org.junit.Test;

public class EcheckTransactionsTests extends BaseTestCase {

    @Before
    public void background() throws Exception {
        iAmDoingCCOrEcheckTransaction();
    }

    @Test
    public void doAVerifiyAndThenSale() {
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

    @Test
    public void testBackendECheckVerifyThenAttemptSale() {
        iAmLoggedInAsAnAdministrator();
        iView("Sales", "Orders");
        iPressCreateNewOrder();
        iClickOnTheCustomerWithEmail("gdake@litle.com");
        iClickAddProducts();
        iAddTheTopRowInProductsToTheOrder();

        fail("Finish implementing me");
    }

    //	@javascript @ready @echeck
    //	Scenario: Backend ECheck verify, then attempt to sale
    //	   Given I am doing Litle auth
    //	   And I am logged in as an administrator
    //	   When I view "Sales" "Orders"
    //	     Then I should see "Orders"
    //	     And I press "Create New Order"
    //	     And I click on the customer "Greg Dake" in "Create New Order"
    //	     And I choose "English"
    //	     And I press "Add Products"
    //	     And I click on the product "affluentvisa"
    //	     And I press "Add Selected Product(s) to Order"
    //	     And I wait for the payments to appear
    //	     And I follow "Get shipping methods and rates"
    //	     And I choose "Fixed Shipping"
    //	     And I choose "LEcheck"
    //	     And I put in "Bank routing number" with "123456000"
    //	     And I put in "Bank account number" with "123456000"
    //	     And I select "Checking" from "Account type"
    //	     And I press "Submit Order"
    //	   When I view "Sales" "Orders"
    //	     Then I should see "Orders"
    //	     And I click on the top row in Orders
    //	       Then I should see "Order #"
    //	     And I press "Invoice"
    //	     And I select "Capture Online" from "invoice[capture_case]"
    //	     And I press "Submit Invoice"
    //	   Then I should see "The invoice has been created."
    //	   And I follow "Log Out"
    //
}

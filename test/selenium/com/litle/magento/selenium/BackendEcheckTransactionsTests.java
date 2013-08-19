package com.litle.magento.selenium;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertFalse;

import org.junit.Before;
import org.junit.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

public class BackendEcheckTransactionsTests extends BaseTestCase {

	@Before
	public void background() throws Exception {
		iAmDoingLitleSale();
		iAmDoingCCOrEcheckTransaction();
		iAmDoingNonPaypageTransaction();
	}
	
	@Test
	public void attemptAFailedVoidSale() throws Exception {
		iAmLoggedInAsWithThePassword("gdake@litle.com", "password");
		iHaveInMyCart("vault");
		iCheckOutWithEcheck("053100300", "13131313","Checking");
		iLogOutAsUser();
		
		iAmLoggedInAsAnAdministrator();
		iView("Sales","Orders");
		iClickOnTheTopRowInOrders();
		iPressVoidSale("The void did not go through. Do a refund instead.");
		iLogOutAsAdministrator();
	}

	@Test
	public void voidCaptureAndThenCaptureAgain() throws Exception {
		iAmLoggedInAsWithThePassword("gdake@litle.com", "password");
		iHaveInMyCart("vault");
		iCheckOutWithEcheck("123456000", "123456000", "Checking");
		iLogOutAsUser();
		
		iAmLoggedInAsAnAdministrator();
		iView("Sales", "Orders");
		iClickOnTheTopRowInOrders();
		iPressVoidSale("The payment has been voided.");
		iPressInvoice();
		iPressSubmitInvoice("The invoice has been created.", "Captured amount of $6.99 online.");
		iLogOutAsAdministrator();
	}

	@Test
	public void successfulCheckoutThenRefundThenVoidRefund() {
		iAmLoggedInAsWithThePassword("gdake@litle.com", "password");
		iHaveInMyCart("vault");
		iCheckOutWithEcheck("123456000", "123456000", "Checking");
		iLogOutAsUser();
		
		iAmLoggedInAsAnAdministrator();
		iView("Sales", "Orders");
		iClickOnTheTopRowInOrders();
		iClickOnInvoices();
		iClickOnTheTopRowInInvoices();
		iPressCreditMemo();
		iPressRefund("The credit memo has been created.");
		iPressVoidRefund("The payment has been voided.");
		iLogOutAsAdministrator();
	}

}

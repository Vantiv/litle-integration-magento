package com.litle.magento.selenium;

import java.sql.ResultSet;

import org.junit.Before;
import org.junit.Test;

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
	    ResultSet rs = stmt.executeQuery("select value from core_config_data where path='payment/LEcheck/payment_action'");
	    rs.next();
	    System.out.println(rs.getString(1));
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
	public void successfulCheckoutThenRefundThenVoidRefund() throws Exception {
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

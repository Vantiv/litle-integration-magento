package com.litle.magento.selenium;

import org.junit.Before;
import org.junit.Test;
import org.openqa.selenium.By;

public class CustomerInformationTests extends BaseTestCase {

	@Before
	public void background() throws Exception {
		iAmDoingCCOrEcheckTransaction();
		iAmDoingNonPaypageTransaction();
	}

	@Test
	public void buyWithVisaAffluentCreditCard() throws Exception {
		iAmLoggedInAsWithThePassword("gdake@litle.com", "password");
		iHaveInMyCart("vault");
		iCheckOutWith("Visa", "4100300018088000");
		iLogOutAsUser();

		iAmLoggedInAsAnAdministrator();
		iView("Customers", "Manage Customers");
		iClickOnTheCustomerWithEmail("gdake@litle.com");
		iClickOnTab("customer_info_tabs","Click here to view Litle & Co. Customer Insight");
		waitFor(By.id("my_custom_tab"));
		iShouldSeeInTheColumnInCustomerInsights("Affluent","Affluence");
		iClickOnTheTopRowInCustomerInsights();
		iLogOutAsAdministrator();
	}

	@Test
	public void buyWithVisaMassAffluentCreditCard() throws Exception {
		iAmLoggedInAsWithThePassword("gdake@litle.com", "password");
		iHaveInMyCart("vault");
		iCheckOutWith("Visa", "4100300023117000");
		iLogOutAsUser();

		iAmLoggedInAsAnAdministrator();
		iView("Customers", "Manage Customers");
		iClickOnTheCustomerWithEmail("gdake@litle.com");
		iClickOnTab("customer_info_tabs","Click here to view Litle & Co. Customer Insight");
		waitFor(By.id("my_custom_tab"));
		iShouldSeeInTheColumnInCustomerInsights("Mass Affluent","Affluence");
		iLogOutAsAdministrator();
	}

	@Test
	public void buyWithPrepaidCard() throws Exception {
		iAmLoggedInAsWithThePassword("gdake@litle.com", "password");
		iHaveInMyCart("vault");
		iCheckOutWith("Visa", "4100323136403000");
		iLogOutAsUser();

		iAmLoggedInAsAnAdministrator();
		iView("Customers", "Manage Customers");
		iClickOnTheCustomerWithEmail("gdake@litle.com");
		iClickOnTab("customer_info_tabs","Click here to view Litle & Co. Customer Insight");
		waitFor(By.id("my_custom_tab"));
		iShouldSeeInTheColumnInCustomerInsights("Gift","Prepaid Card Type");
		iShouldSeeInTheColumnInCustomerInsights("Prepaid","Funding Source");
		iShouldSeeInTheColumnInCustomerInsights("$15.00","Available Balance");
		iShouldSeeInTheColumnInCustomerInsights("No","Reloadable");
		iLogOutAsAdministrator();
	}

	@Test
	public void testBuyWithIssuingCountryCard() throws Exception {
		iAmLoggedInAsWithThePassword("gdake@litle.com", "password");
		iHaveInMyCart("vault");
		iCheckOutWith("Visa", "4100300002271000");
		iLogOutAsUser();

		iAmLoggedInAsAnAdministrator();
		iView("Customers", "Manage Customers");
		iClickOnTheCustomerWithEmail("gdake@litle.com");
		iClickOnTab("customer_info_tabs","Click here to view Litle & Co. Customer Insight");
		waitFor(By.id("my_custom_tab"));
		iShouldSeeInTheColumnInCustomerInsights("BRA","Issuing Country");
		iLogOutAsAdministrator();
	}
}

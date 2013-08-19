package com.litle.magento.selenium;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertFalse;

import org.junit.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

public class AccountUpdaterTests extends BaseTestCase {

	@Test
	public void doASuccessfulAuthAndThenCaptureTheAuth() throws Exception {
		iAmDoingCCOrEcheckTransaction();
		iAmDoingNonPaypageTransaction();
		iAmDoingLitleAuth();
		iAmLoggedInAsWithThePassword("gdake@litle.com","password");
		iHaveInMyCart("vault");
		iCheckOutWith("Visa","4000162019882000");
		iLogOutAsUser();
		iAmLoggedInAsAnAdministrator();
		iView("Sales","Orders");
		iClickOnTheTopRowInOrders();
		WebElement e = driver.findElement(By.xpath("/html/body/div[2]/div[3]/div/div/div[2]/div/div[3]/div/div/div[8]/div/fieldset/table/tbody/tr[2]/td"));
		assertEquals("Credit Card Number:", e.getText());
		e = driver.findElement(By.xpath("/html/body/div[2]/div[3]/div/div/div[2]/div/div[3]/div/div/div[8]/div/fieldset/table/tbody/tr[2]/td[2]"));
		assertFalse(e.getText(),e.getText().contains("2000")); //the last 4 digits changed due to AU
		iLogOutAsAdministrator();
	}
}

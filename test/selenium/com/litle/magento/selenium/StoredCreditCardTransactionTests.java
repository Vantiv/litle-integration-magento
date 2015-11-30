package com.litle.magento.selenium;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertFalse;
import static org.junit.Assert.assertTrue;

import java.sql.ResultSet;

import org.junit.Before;
import org.junit.Test;
import org.openqa.selenium.Alert;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

public class StoredCreditCardTransactionTests extends BaseTestCase {

    @Before
    public void setUp() throws Exception {
        iAmDoingCCOrEcheckTransaction();
        iAmDoingStoredCards();
    }

	@Test
	public void doASuccessfulCheckkoutWithStoredCreditCard() throws Exception {
	    iAmDoingLitleAuth();
	    iAmLoggedInAsWithThePassword("abc@gmail.com", "password");
	    iHaveInMyCart("vault");
	    iCheckOutWith("Visa", "4100280190123000", true);
        iHaveInMyCart("vault");
        iCheckOutWith("Visa", "Stored Visa Ending in: 3000");

        theVaultTableHas("3000", "%3000", "VI", "410028");

        //Verify my account lists the card
        //Click My Account
        driver.findElement(By.linkText("My Account")).click();
        waitFor(By.linkText("Stored Credit Cards"));
        //Click stored credit cards
        driver.findElement(By.linkText("Stored Credit Cards")).click();
        //Check the text of the box
        WebElement infoBox = driver.findElement(By.className("info-box"));
        String infoText = infoBox.getText();
        assertTrue(infoText, infoText.contains("Visa"));
        assertTrue(infoText, infoText.contains("Ends in 3000"));
        //Delete the stored card
        infoBox.findElement(By.linkText("Delete")).click();
        Alert alert = driver.switchTo().alert();
        alert.accept();
        waitFor(By.className("success-msg"));
        String message = driver.findElement(By.className("success-msg")).getText();
        assertTrue(message, message.contains("The card has been deleted"));
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
	public void doASuccessfulCheckkoutWithStoredCreditCard_Amex() throws Exception {
System.out.println("TEST RUNS");
	    iAmDoingLitleAuth();
	    iAmLoggedInAsWithThePassword("abc@gmail.com", "password");

	    iHaveInMyCart("vault");
	    iCheckOutWith("American Express", "370028010000000", true);

        iHaveInMyCart("vault");
        iCheckOutWith("American Express", "Stored American Express Ending in: 0000");

        theVaultTableHas("0000", "%0000", "AE", "370028");

        //Verify my account lists the card
        //Click My Account
        driver.findElement(By.linkText("My Account")).click();
        waitFor(By.linkText("Stored Credit Cards"));
        //Click stored credit cards
        driver.findElement(By.linkText("Stored Credit Cards")).click();
        //Check the text of the box
        WebElement infoBox = driver.findElement(By.className("info-box"));
        String infoText = infoBox.getText();
        assertTrue(infoText, infoText.contains("American Express"));
        assertTrue(infoText, infoText.contains("Ends in 0000"));
        //Delete the stored card
        infoBox.findElement(By.linkText("Delete")).click();
        Alert alert = driver.switchTo().alert();
        alert.accept();
        waitFor(By.className("success-msg"));
        String message = driver.findElement(By.className("success-msg")).getText();
        assertTrue(message, message.contains("The card has been deleted"));
	    iLogOutAsUser();

	    iAmLoggedInAsAnAdministrator();
	    iView("Sales", "Orders");
	    iClickOnTheTopRowInOrders();
	    iPressInvoice();
	    iSelectNameFromSelect("Capture Online", "invoice[capture_case]");
	    iPressSubmitInvoice("The invoice has been created.", null);
	    iLogOutAsAdministrator();
	}

    private void theVaultTableHas(String last4, String tokenLike, String type, String bin) throws Exception {
        ResultSet rs = stmt.executeQuery("select * from litle_vault where token like '"+tokenLike+"'");
        assertTrue(rs.next());
        assertEquals(last4, rs.getString("last4"));
        assertEquals(type, rs.getString("type"));
        assertEquals(bin, rs.getString("bin"));
        assertFalse(rs.next());
    }

}

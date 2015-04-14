package com.litle.magento.selenium;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertTrue;
import static org.junit.Assert.fail;

import java.util.List;

import org.junit.Before;
import org.junit.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

public class TransactionDetailTests extends BaseTestCase {

    @Before
    public void setUp() throws Exception {
        iAmDoingCCOrEcheckTransaction();
        iAmDoingPaypageTransaction();
    }

	@Test
	public void transactionDetailHasLinksToLitle() throws Exception {
	    iAmDoingLitleAuth();
	    iAmLoggedInAsWithThePassword("abc@gmail.com", "password");
	    iHaveInMyCart("vault");
	    iCheckOutWith("Visa", "4100000000000001");
	    iLogOutAsUser();

	    iAmLoggedInAsAnAdministrator();

	    iView("Sales", "Orders");
	    iClickOnTheTopRowInOrders();
	    iPressInvoice();
	    iSelectNameFromSelect("Capture Online", "invoice[capture_case]");
	    iPressSubmitInvoice("The invoice has been created.", null);

	    iView("Sales", "Transactions");
	    //There should be 2 rows
	    WebElement table = driver.findElement(By.id("order_transactions_table"));
	    List<WebElement> rows = table.findElement(By.tagName("tbody")).findElements(By.tagName("tr"));
	    assertEquals(2, rows.size());
	    WebElement firstRow = rows.get(0);
	    firstRow.click();
	    //The first row is the capture
	    String parentUrl = verifyTransactionDetailTable("capture");
	    driver.get(parentUrl);
	    verifyTransactionDetailTable("authorization");

	    iLogOutAsAdministrator();
	}

	private String verifyTransactionDetailTable(String transactionType) {
        waitFor(By.id("log_details_fieldset"));
        WebElement table = driver.findElement(By.id("log_details_fieldset")).findElement(By.tagName("table")).findElement(By.tagName("tbody"));
        List<WebElement> rows = table.findElements(By.tagName("tr"));
        assertEquals(6, rows.size());
        assertEquals("Transaction ID", rows.get(0).findElement(By.tagName("th")).getText());
        assertEquals("Parent Transaction ID", rows.get(1).findElement(By.tagName("th")).getText());
        assertEquals("Transaction Type", rows.get(3).findElement(By.tagName("th")).getText());
        String transactionUrl = rows.get(0).findElement(By.tagName("a")).getAttribute("href");
        if("capture".equals(transactionType)) {
            assertTrue(transactionUrl, transactionUrl.startsWith("https://www.testlitle.com/sandbox/ui/reports/payments/deposit/"));
        }
        else if("authorization".equals(transactionType)) {
            assertTrue(transactionUrl, transactionUrl.startsWith("https://www.testlitle.com/sandbox/ui/reports/payments/authorization/"));
        }
        else {
            fail("Unrecognized transaction type :" + transactionType + ":");
        }
        String transactionTypeFromTable = rows.get(3).findElement(By.tagName("td")).getText();
        assertEquals(transactionType, transactionTypeFromTable);
        String parentUrl = null;
        if(!transactionType.equals("authorization")) {
            parentUrl = rows.get(1).findElement(By.tagName("a")).getAttribute("href");
        }
	    return parentUrl;
	}



}

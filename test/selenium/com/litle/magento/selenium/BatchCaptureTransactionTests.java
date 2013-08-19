package com.litle.magento.selenium;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertFalse;
import static org.junit.Assert.assertTrue;

import java.sql.ResultSet;
import java.util.List;

import org.junit.Before;
import org.junit.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

public class BatchCaptureTransactionTests extends BaseTestCase {

	@Before
	public void background() throws Exception {
		iAmDoingCCOrEcheckTransaction();
		iAmDoingNonPaypageTransaction();
	}
	
	@Test
	public void doAsuccessfulBatchCaptureOfTwoAuthorizedTransactionsAndAnUnsucessfulOneWithACapturedTransaction() throws Exception {
		iAmDoingLitleAuth();
		
		iAmLoggedInAsWithThePassword("gdake@litle.com", "password");
		iHaveInMyCart("vault");
		iCheckOutWith("Visa","4100000000000001");
		iHaveInMyCart("vault");
		iCheckOutWith("Visa","4100000000000001");
		iHaveInMyCart("vault");
		iCheckOutWith("Visa","4100000000000001");
		iLogOutAsUser();
		
		iAmLoggedInAsAnAdministrator();
		iView("Sales", "Orders");
		String orderNum = getOrderNumForOrder(2);
		System.out.println("Order number for row 2 is: " + orderNum);
		String sql = "select entity_id from sales_flat_order where increment_id = " + orderNum;
		System.out.println("Executing sql: " + sql);
		ResultSet rs = stmt.executeQuery(sql);
		assertTrue(rs.next());
		String orderEntityId = rs.getString(1);
		System.out.println("Entity id for that row is: " + orderEntityId);
		int numRows = stmt.executeUpdate("update sales_payment_transaction set txn_id = 123456789012345361 where order_id = " + orderEntityId);
		assertEquals(1, numRows);
		iSelectTopOrders(3);
		iSelectFromSelect("Capture", "sales_order_grid_massaction-select");
		iPressSubmitOnOrders();
		
		WebElement errorMsgDiv = driver.findElement(By.className("error-msg"));
		List<WebElement> errorMsgList = errorMsgDiv.findElements(By.tagName("li"));
		assertEquals(1, errorMsgList.size());
		String actualErrorMsg = errorMsgList.get(0).getText();
		rs = stmt.executeQuery("select litle_txn_id from litle_failed_transactions");
		rs.next();
		String litleTxnId = rs.getString(1);
		String expectedErrorMsg = "The order #"+orderNum+" can not be captured. Authorization no longer available. The authorization for this transaction is no longer available; the authorization has already been consumed by another capture.For your reference, the transaction id is "+litleTxnId;
		assertEquals(expectedErrorMsg, actualErrorMsg);
		
		WebElement successMsgDiv = driver.findElement(By.className("success-msg"));
		List<WebElement> successMsgList = successMsgDiv.findElements(By.tagName("li"));
		assertEquals(2, successMsgList.size());
		String actualSuccessMsg = successMsgList.get(0).getText();
		String expectedSuccessMsg = "The order #"+getOrderNumForOrder(0)+" captured successfully";
		assertEquals(expectedSuccessMsg, actualSuccessMsg);
		
		actualSuccessMsg = successMsgList.get(1).getText();
		expectedSuccessMsg = "The order #"+getOrderNumForOrder(1)+" captured successfully";
		assertEquals(expectedSuccessMsg, actualSuccessMsg);
		
		iLogOutAsAdministrator();
	}
}

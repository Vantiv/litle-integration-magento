package com.litle.magento.selenium;

import static org.junit.Assert.assertEquals;

import org.junit.Test;
import org.openqa.selenium.By;

public class ReportsTests extends BaseTestCase {

	@Test
	public void transactionDetailHasLinksToLitle() throws Exception {
	    iAccessAReportingUrl("/palorus/adminhtml_myform/search/");
	    waitFor(By.linkText("sdksupport@litle.com"));
	    String reportingUrl = driver.getCurrentUrl();
	    assertEquals(reportingUrl, "https://www.testlitle.com/sandbox/ui/transactions/search");
	}
}

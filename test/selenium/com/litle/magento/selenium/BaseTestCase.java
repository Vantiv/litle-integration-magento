package com.litle.magento.selenium;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertNotNull;
import static org.junit.Assert.assertTrue;

import java.io.File;
import java.io.IOException;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.Statement;
import java.util.List;

import org.apache.commons.io.FileUtils;
import org.junit.After;
import org.junit.AfterClass;
import org.junit.Before;
import org.junit.BeforeClass;
import org.openqa.selenium.Alert;
import org.openqa.selenium.By;
import org.openqa.selenium.OutputType;
import org.openqa.selenium.TakesScreenshot;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.firefox.FirefoxProfile;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.events.AbstractWebDriverEventListener;
import org.openqa.selenium.support.events.EventFiringWebDriver;
import org.openqa.selenium.support.events.WebDriverEventListener;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

public class BaseTestCase {

    static String MAGENTO_DB_NAME = System.getenv("MAGENTO_DB_NAME");
    static String MAGENTO_DB_USER = System.getenv("MAGENTO_DB_USER");
    static String MAGENTO_HOME = System.getenv("MAGENTO_HOME");
    static final long DEFAULT_TIMEOUT = 60;
    static String CONTEXT = "magento1702";
    private static String JDBC_URL;
    private static Connection conn;
    static Statement stmt;
    static EventFiringWebDriver driver;
    static WebDriverWait wait;

    @BeforeClass
    public static void setupSuite() throws Exception {
        String[] cmd = new String[] {"rm","-rf",MAGENTO_HOME+"/var/cache/*"};
        Runtime.getRuntime().exec(cmd);
        //JDBC_URL = "jdbc:mysql://localhost:3306/" + MAGENTO_DB_NAME;
        JDBC_URL = "jdbc:mysql://localhost:3306/magento1702";
        Class.forName("com.mysql.jdbc.Driver");
        conn = DriverManager.getConnection(JDBC_URL, MAGENTO_DB_USER, "");
        stmt = conn.createStatement();
        stmt.executeUpdate("delete from core_resource where code = 'palorus_setup'");
        stmt.executeUpdate("delete from core_resource where code = 'lecheck_setup'");
        stmt.executeUpdate("delete from core_resource where code = 'creditcard_setup'");
        stmt.executeUpdate("delete from core_config_data where path like 'payment/CreditCard/%'");
        stmt.executeUpdate("delete from core_config_data where path like 'payment/LEcheck/%'");

        stmt.executeUpdate("delete from catalog_eav_attribute where attribute_id = (select attribute_id from eav_attribute where attribute_code = 'litle_subscription')");
        stmt.executeUpdate("delete from `eav_entity_attribute` where attribute_id = (select attribute_id from eav_attribute where attribute_code = 'litle_subscription')");
        stmt.executeUpdate("delete from eav_attribute where attribute_code = 'litle_subscription'");

        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/active','1')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/title','Credit Card')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/user','USER')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/password','PASSWORD')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/merchant_id','(''USD''=>''101'')')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/order_status','processing')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/payment_action','authorize')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/url','https://www.testlitle.com/sandbox/communicator/online')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/paypage_enable','0')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/paypage_url',null)");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/paypage_id',null)");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/timeout',null)");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/LEcheck/active','0')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/LEcheck/title',null)");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/LEcheck/payment_action','authorize')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/LEcheck/order_status',null)");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/proxy','iwp1.lowell.litle.com:8080')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/cctypes','AE,DC,VI,MC,DI,JCB')");

        cmd = new String[] {"rm","-f",MAGENTO_HOME+"/var/log/*"};
        Runtime.getRuntime().exec(cmd);
    }

    @Before
    public void before() throws Exception {
        System.setProperty("webdriver.firefox.bin","/usr/local/litle-home/gdake/firefox/firefox");
        //FirefoxProfile profile = new FirefoxProfile(new File("/usr/local/litle-home/gdake/.mozilla/firefox/wzy1h2qp.magento"));
        //driver = new FirefoxDriver(profile);
        FirefoxProfile profile = new FirefoxProfile();
        profile.setEnableNativeEvents(true);
        driver = new EventFiringWebDriver(new FirefoxDriver(profile));
        wait = new WebDriverWait(driver, DEFAULT_TIMEOUT);
        WebDriverEventListener errorListener = new AbstractWebDriverEventListener() {
            @Override
            public void onException(Throwable throwable, WebDriver driver) {
                takeScreenshot(driver.getTitle() + "-" + String.valueOf(System.currentTimeMillis()));
            }

            private void takeScreenshot(String screenshotName) {
                File tempFile = ((TakesScreenshot)driver).getScreenshotAs(OutputType.FILE);
                try {
                    FileUtils.copyFile(tempFile, new File("/usr/local/litle-home/gdake/git/litle-integration-magento/test/screenshots/" + screenshotName + ".png"));
                } catch (IOException e) {
                    e.printStackTrace();
                }
            }

        };
        driver.register(errorListener);

        stmt.executeUpdate("delete from litle_failed_transactions");
        stmt.executeUpdate("delete from litle_customer_insight");

    }



    @AfterClass
    public static void tearDownSuite() throws Exception {
        stmt.close();
        conn.close();
    }

    @After
    public void after() {
        // if($event->getResult() == 4) { //Failure
        // $dbName = getenv('MAGENTO_DB_NAME');
        // $dbUser = getenv('MAGENTO_DB_USER');
        // $sql = <<<EOD
        // mysql -u $dbUser $dbName -e
        // "select path,value from core_config_data where path like 'payment/CreditCard/%'"
        // EOD;
        // system($sql);
        // $sql = <<<EOD
        // mysql -u $dbUser $dbName -e
        // "select path,value from core_config_data where path like 'payment/LEcheck/%'"
        // EOD;
        // system($sql);
        // }

        //driver.quit();
    }

    static void iAmDoingCCOrEcheckTransaction() throws Exception {
        stmt.executeUpdate("update core_config_data set value='https://www.testlitle.com/sandbox/communicator/online' where path='payment/CreditCard/url'");

        stmt.executeUpdate("update core_config_data set value='' where path='payment/CreditCard/proxy'");

        stmt.executeUpdate("update core_config_data set value='1' where path='payment/LEcheck/active'");
        stmt.executeUpdate("update core_config_data set value='1' where path='payment/CreditCard/active'");

        stmt.executeUpdate("update core_config_data set value='JENKINS' where path='payment/CreditCard/user'");
        stmt.executeUpdate("update core_config_data set value='PayPageTransactions' where path='payment/CreditCard/password'");
        stmt.executeUpdate("update core_config_data set value='(\"USD\"=>\"101\",\"GBP\"=>\"102\")' where path='payment/CreditCard/merchant_id'");
        stmt.executeUpdate("update core_config_data set value='AE,DC,VI,MC,DI,JCB' where path='payment/CreditCard/cctypes'");
        stmt.executeUpdate("update core_config_data set value='Checking,Savings,Corporate,Corp Savings' where path='payment/LEcheck/accounttypes'");
        stmt.executeUpdate("update core_config_data set value='Litle ECheck' where path='payment/LEcheck/title'");
        stmt.executeUpdate("update core_config_data set value='Litle Credit Card' where path='payment/CreditCard/title'");
        stmt.executeUpdate("update core_config_data set value='iwp1.lowell.litle.com:8080' where path='payment/CreditCard/proxy'");

        stmt.executeUpdate("delete from sales_flat_order");
        stmt.executeUpdate("delete from sales_flat_quote");
    }

    static void iAmDoingNonPaypageTransaction() throws Exception {
        stmt.executeUpdate("update core_config_data set value='0' where path='payment/CreditCard/paypage_enable'");
    }

    void iAmDoingLitleAuth() throws Exception {
        stmt.executeUpdate("update core_config_data set value='authorize' where path='payment/CreditCard/payment_action'");
        stmt.executeUpdate("update core_config_data set value='authorize' where path='payment/LEcheck/payment_action'");
    }

    void iAmDoingLitleSale() throws Exception {
        stmt.executeUpdate("update core_config_data set value='authorize_capture' where path='payment/CreditCard/payment_action'");
        stmt.executeUpdate("update core_config_data set value='authorize_capture' where path='payment/LEcheck/payment_action'");
    }

    void iAmLoggedInAsWithThePassword(String username, String password) {
        driver.get("http://localhost/" + CONTEXT + "/index.php/");

        //Get to login screen
        driver.findElement(By.linkText("Log In")).click();
        waitForIdVisible("email");

        //Login
        driver.findElement(By.id("email")).clear();
        driver.findElement(By.id("email")).sendKeys(username);
        driver.findElement(By.id("pass")).clear();
        driver.findElement(By.id("pass")).sendKeys(password);
        driver.findElement(By.id("send2")).click(); //click login button
        waitForCssVisible("html body.customer-account-index div.wrapper div.page div.main-container div.main div.col-main div.my-account div.dashboard div.page-title h1");
    }

    void waitForIdVisible(String id) {
        wait.until(ExpectedConditions.visibilityOfElementLocated(By.id(id)));
    }

    void waitForCssVisible(String css) {
        wait.until(ExpectedConditions.visibilityOfElementLocated(By.cssSelector(css)));
    }

    void waitForClassVisible(String className) {
        wait.until(ExpectedConditions.visibilityOfElementLocated(By.className(className)));
    }

    void waitFor(By locator) {
        wait.until(ExpectedConditions.visibilityOfElementLocated(locator));
    }

    void iHaveInMyCart(String productName) {
        waitFor(By.id("search"));
        //Find the item

        //Enter search text
        WebElement e = driver.findElement(By.id("search"));
        e.clear();
        e.sendKeys(productName);

        //Hit the search button
        e = driver.findElement(By.className("form-search"));
        e = e.findElement(By.tagName("button"));
        e.click();
        waitForCssVisible(".btn-cart");

        //Add to cart
        e = driver.findElement(By.cssSelector(".btn-cart"));
        e.click();
        waitForCssVisible(".btn-proceed-checkout");
    }

    void iLogOutAsAdministrator() {
        driver.findElement(By.linkText("Log Out")).click();
        waitFor(By.linkText("Forgot your password?"));
    }

    void iClickOnTheTopRowInOrders() {
        WebElement topRow = driver.findElement(By.xpath("/html/body/div/div[3]/div/div[3]/div/div[2]/div/table/tbody/tr[1]"));
        String title = topRow.getAttribute("title");
        driver.get(title);
        waitFor(By.className("head-billing-address"));
    }

    void iClickOnTheTopRowInCustomerInsights() {
        WebElement topRow = driver.findElement(By.xpath("/html/body/div/div[3]/div/div/div[2]/div/div[3]/form/div[3]/div/div/div/table/tbody/tr"));
        String title = topRow.getAttribute("title");
        driver.get(title);
        waitFor(By.className("head-billing-address"));
    }

    void iAddTheTopRowInProductsToTheOrder() {
        WebElement topRow = driver.findElement(By.xpath("/html/body/div/div[3]/div/form/div[5]/div/div/table/tbody/tr/td[2]/div[2]/div/div[2]/div/div/div/table/tbody/tr"));
        topRow.click();
        WebElement e = driver.findElement(By.id("order-search"));
        e = e.findElement(By.className("entry-edit-head"));
        e = e.findElement(By.tagName("button"));
        e.click();
        waitFor(By.cssSelector("html body#html-body.adminhtml-sales-order-create-index div.wrapper div#anchor-content.middle div#page:main-container form#edit_form div#order-data div div.page-create-order table tbody tr td.main-col div#order-items div div.entry-edit div table tbody tr td.a-right button .scalable"));
    }

    void iClickAddProducts() {
        WebElement e = driver.findElement(By.id("order-items"));
        e = e.findElement(By.tagName("button"));
        e.click();
        waitForClassVisible("head-catalog-product");
    }

    void iClickOnTheCustomerWithEmail(String email) {
        if("New Order / Orders / Sales / Magento Admin".equals(driver.getTitle())) {
            WebElement e = driver.findElement(By.id("sales_order_create_customer_grid_table"));
            e = e.findElement(By.tagName("tbody"));
            List<WebElement> rows = e.findElements(By.tagName("tr"));
            WebElement rowToClick = null;
            for(WebElement row : rows) {
                WebElement emailCol = row.findElements(By.tagName("td")).get(2);
                String colValue = emailCol.getText().trim();
                if(colValue.equals(email)) {
                    rowToClick = row;
                }
            }
            assertNotNull("Couldn't find customer with email " + email, rowToClick);
            rowToClick.click();
            waitFor(By.id("submit_order_top_button"));
        }
        else {
            WebElement e = driver.findElement(By.id("customerGrid_table"));
            e = e.findElement(By.tagName("tbody"));
            List<WebElement> rows = e.findElements(By.tagName("tr"));
            String url = null;
            for(WebElement row : rows) {
                WebElement emailCol = row.findElements(By.tagName("td")).get(3);
                String colName = emailCol.getText().trim();
                if(colName.equals(email)) {
                    url = row.getAttribute("title");
                }
            }
            assertNotNull("Couldn't find customer with email " + email, url);
            driver.get(url);
            waitFor(By.id("customer_info_tabs"));
        }
    }

    //"customer_info_tabs","Click here to view Litle & Co. Customer Insight"
    void iClickOnTab(String tabsId, String linkTitleToFollow) {
        WebElement e = driver.findElement(By.id(tabsId));
        WebElement url = null;
        List<WebElement> links = e.findElements(By.tagName("a"));
        for(WebElement link : links) {
            if(link.getAttribute("title").trim().equals(linkTitleToFollow)) {
                url = link;
            }
        }
        assertNotNull("Couldn't find link titled " + linkTitleToFollow + " on tab " + tabsId, url);
        url.click();
    }

    //iShouldSeeInTheColumnInCustomerInsights("Affluent","Affluence");
    void iShouldSeeInTheColumnInCustomerInsights(String expectedValue,String columnName) {
        //Find the column with the correct name
        WebElement e = driver.findElement(By.id("my_custom_tab_table"));
        List<WebElement> headings = e.findElements(By.tagName("th"));
        int correctHeadingIndex = -1;
        for(int i = 0; i < headings.size(); i++) {
            WebElement heading = headings.get(i);
            String columnHeading = heading.getText();
            if(columnName.equals(columnHeading)) {
                correctHeadingIndex = i;
            }
        }
        assertTrue("Couldn't find column named " + columnName + " on customer insights", correctHeadingIndex != -1);

        List<WebElement> cols = e.findElement(By.tagName("tbody")).findElements(By.tagName("td"));
        WebElement columnn = cols.get(correctHeadingIndex);
        assertEquals(expectedValue, columnn.getText().trim());
    }

    void iView(String menu, String subMenu)
    {
        WebElement e = driver.findElement(By.id("nav"));
        WebElement firstMenuElement = e.findElement(By.linkText(menu));
        Actions mouseOver = new Actions(driver);
        mouseOver.moveToElement(firstMenuElement).build().perform();
        WebElement secondMenuElement = e.findElement(By.linkText(subMenu));
        secondMenuElement.click();
        waitFor(By.className("content-header"));

        e = driver.findElement(By.className("content-header"));
        e = e.findElement(By.tagName("h3"));
        assertEquals(subMenu, e.getText());

        if("Orders".equals(subMenu)) {
            waitFor(By.id("sales_order_grid"));
        } else if("Manage Customers".equals(subMenu)) {
            waitFor(By.id("customerGrid"));
        }
    }


    void iAmLoggedInAsAnAdministrator() {
        //Get to login screen
        driver.get("http://localhost/" + CONTEXT + "/index.php/admin");
        waitForIdVisible("username");

        //Enter username
        WebElement e = driver.findElement(By.id("username"));
        e.clear();
        e.sendKeys("admin");

        //Enter password
        e = driver.findElement(By.id("login"));
        e.clear();
        e.sendKeys("LocalMagentoAdmin1");

        //Click login button
        driver.findElement(By.className("form-button")).click();
        waitForClassVisible("link-logout");
    }

    void iCheckOutWith(String ccType, String creditCardNumber) {
        //And I press "Proceed to Checkout"
        WebElement e = driver.findElement(By.className("btn-proceed-checkout"));
        e.click();
        waitForIdVisible("co-billing-form");

        //And I press "Continue"
        e = driver.findElement(By.id("co-billing-form"));
        e = e.findElement(By.tagName("button"));
        e.click();
        waitForIdVisible("co-shipping-method-form");

        //	      And I press the "3rd" continue button - Shipping Method
        e = driver.findElement(By.id("co-shipping-method-form"));
        e = e.findElement(By.tagName("button"));
        e.click();
        waitForIdVisible("co-payment-form");

        //And I choose "CreditCard"
        e = driver.findElement(By.id("p_method_creditcard"));
        e.click();
        waitForIdVisible("creditcard_cc_type");

        //And I select "Visa" from "Credit Card Type"
        iSelectFromSelect(ccType, "creditcard_cc_type");

        //	      And I put in "Credit Card Number" with "4000162019882000"
        e = driver.findElement(By.id("creditcard_cc_number"));
        e.clear();
        e.sendKeys(creditCardNumber);

        //	      And I select "9" from "Expiration Date"
        iSelectFromSelect("09 - September", "creditcard_expiration");

        //	      And I select "2012" from "creditcard_expiration_yr"
        iSelectFromSelect("2015", "creditcard_expiration_yr");

        //	      And I put in "Card Verification Number" with "123"
        e = driver.findElement(By.id("creditcard_cc_cid"));
        e.clear();
        e.sendKeys("123");

        //	      And I press the "4th" continue button
        e = driver.findElement(By.id("payment-buttons-container"));
        e = e.findElement(By.tagName("button"));
        e.click();
        waitForIdVisible("checkout-step-review");

        //	      And I press "Place Order"
        e = driver.findElement(By.id("review-buttons-container"));
        e = e.findElement(By.tagName("button"));
        e.click();

        //	    Then I should see "Thank you for your purchase"
        waitForCssVisible("html body.checkout-onepage-success div.wrapper div.page div.main-container div.main div.col-main p a");
        e = driver.findElement(By.className("col-main"));
        e = e.findElement(By.className("sub-title"));
        assertEquals("Thank you for your purchase!",e.getText());
    }

    void iCheckOutWithEcheck(String routingNumber, String accountNumber, String accountType) {
        //And I press "Proceed to Checkout"
        WebElement e = driver.findElement(By.className("btn-proceed-checkout"));
        e.click();
        waitForIdVisible("co-billing-form");

        //And I press "Continue"
        e = driver.findElement(By.id("co-billing-form"));
        e = e.findElement(By.tagName("button"));
        e.click();
        waitForIdVisible("co-shipping-method-form");

        //	      And I press the "3rd" continue button - Shipping Method
        e = driver.findElement(By.id("co-shipping-method-form"));
        e = e.findElement(By.tagName("button"));
        e.click();
        waitForIdVisible("co-payment-form");

        //And I choose "Echeck"
        e = driver.findElement(By.id("p_method_lecheck"));
        e.click();
        waitForIdVisible("lecheck_echeck_routing_number");

        e = driver.findElement(By.id("lecheck_echeck_routing_number"));
        e.clear();
        e.sendKeys(routingNumber);

        e = driver.findElement(By.id("lecheck_echeck_bank_acct_num"));
        e.clear();
        e.sendKeys(accountNumber);

        iSelectFromSelect(accountType, "lecheck_echeck_account_type");

        //	      And I press the "4th" continue button
        e = driver.findElement(By.id("payment-buttons-container"));
        e = e.findElement(By.tagName("button"));
        e.click();
        waitForIdVisible("checkout-step-review");

        //	      And I press "Place Order"
        e = driver.findElement(By.id("review-buttons-container"));
        e = e.findElement(By.tagName("button"));
        e.click();

        //	    Then I should see "Thank you for your purchase"
        waitForCssVisible("html body.checkout-onepage-success div.wrapper div.page div.main-container div.main div.col-main p a");
        e = driver.findElement(By.className("col-main"));
        e = e.findElement(By.className("sub-title"));
        assertEquals("Thank you for your purchase!",e.getText());
    }

    void iLogOutAsUser() {
        driver.findElement(By.linkText("Log Out")).click();
        waitFor(By.partialLinkText("Log In"));
    }

    void iSelectFromSelect(String optionText, String selectId) {
        WebElement select = driver.findElement(By.id(selectId));
        List<WebElement> options = select.findElements(By.tagName("option"));
        for(WebElement option : options){
            if(option.getText().equals(optionText)) {
                option.click();
                break;
            }
        }
    }

    void iPressInvoice() {
        WebElement invoiceButton = null;
        List<WebElement> buttons = driver.findElement(By.id("anchor-content")).findElement(By.className("form-buttons")).findElements(By.tagName("button"));
        for(WebElement button : buttons) {
            if("Invoice".equals(button.getAttribute("title"))) {
                invoiceButton = button;
            }
        }
        assertNotNull("Couldn't find invoice button", invoiceButton);
        waitFor(By.id(invoiceButton.getAttribute("id")));
        invoiceButton.click();
        waitFor(By.className("order-totals-bottom"));
    }

    void iPressCreateNewOrder() {
        WebElement createOrderButton = null;
        List<WebElement> buttons = driver.findElement(By.id("anchor-content")).findElement(By.className("form-buttons")).findElements(By.tagName("button"));
        for(WebElement button : buttons) {
            if("Create New Order".equals(button.getAttribute("title"))) {
                createOrderButton = button;
            }
        }
        assertNotNull("Couldn't find create new order button", createOrderButton);
        waitFor(By.id(createOrderButton.getAttribute("id")));
        createOrderButton.click();
        waitFor(By.id("back_order_top_button"));
    }

    void iPressSubmitInvoice(String expectedMessage, String expectedComment) {
        WebElement e = driver.findElement(By.className("order-totals-bottom"));
        e = e.findElement(By.tagName("button"));
        e.click();
        waitFor(By.id("messages"));

        if(expectedMessage != null) {
            e = driver.findElement(By.id("messages"));
            assertEquals(expectedMessage,e.getText());
        }

        if(expectedComment != null) {
            List<WebElement> comments = driver.findElement(By.id("order_history_block")).findElements(By.tagName("li"));
            assertTrue(String.valueOf(comments.size()), comments.size() > 0);
            e = comments.get(0);
            assertTrue(e.getText(), e.getText().contains(expectedComment));
        }
    }

    void iPressVoidCapture(String expectedMessage) {
        WebElement voidCaptureButton = null;
        List<WebElement> buttons = driver.findElement(By.id("content")).findElement(By.className("form-buttons")).findElements(By.tagName("button"));
        for(WebElement button : buttons) {
            if("Void Capture".equals(button.getAttribute("title"))) {
                voidCaptureButton = button;
            }
        }
        assertNotNull("Couldn't find void capture button", voidCaptureButton);
        waitFor(By.id(voidCaptureButton.getAttribute("id")));
        voidCaptureButton.click();
        Alert alert = driver.switchTo().alert();
        alert.accept();
        waitFor(By.id("messages"));

        String actualText = driver.findElement(By.id("messages")).getText();
        assertTrue(actualText, actualText.matches(expectedMessage));
    }

    void iPressVoidSale(String expectedMessage) {
        WebElement voidSaleButton = null;
        List<WebElement> buttons = driver.findElement(By.id("content")).findElement(By.className("form-buttons")).findElements(By.tagName("button"));
        for(WebElement button : buttons) {
            if("Void Sale".equals(button.getAttribute("title"))) {
                voidSaleButton = button;
            }
        }
        assertNotNull("Couldn't find void sale button", voidSaleButton);
        waitFor(By.id(voidSaleButton.getAttribute("id")));
        voidSaleButton.click();
        Alert alert = driver.switchTo().alert();
        alert.accept();
        waitFor(By.id("messages"));
        assertEquals(expectedMessage, driver.findElement(By.id("messages")).getText());
    }

    void iPressCreditMemo() {
        WebElement creditMemoButton = null;
        List<WebElement> buttons = driver.findElement(By.id("anchor-content")).findElement(By.className("form-buttons")).findElements(By.tagName("button"));
        for(WebElement button : buttons) {
            if("Credit Memo".equals(button.getAttribute("title"))) {
                creditMemoButton = button;
            }
        }
        assertNotNull("Couldn't find credit memo button", creditMemoButton);
        waitFor(By.id(creditMemoButton.getAttribute("id")));
        creditMemoButton.click();
        waitFor(By.id("edit_form"));
    }

    void iPressRefund(String message) {
        WebElement refundButton = null;
        List<WebElement> buttons = driver.findElement(By.id("creditmemo_item_container")).findElement(By.className("order-totals-bottom")).findElements(By.tagName("button"));
        for(WebElement button : buttons) {
            if("Refund".equals(button.getAttribute("title"))) {
                refundButton = button;
            }
        }
        assertNotNull("Couldn't find refund button", refundButton);
        waitFor(By.id(refundButton.getAttribute("id")));
        refundButton.click();
        waitFor(By.id("messages"));
        assertEquals(message, driver.findElement(By.id("messages")).getText());
    }

    void iPressVoidRefund(String message) {
        WebElement refundButton = null;
        List<WebElement> buttons = driver.findElement(By.id("content")).findElement(By.className("form-buttons")).findElements(By.tagName("button"));
        for(WebElement button : buttons) {
            if("Void Refund".equals(button.getAttribute("title"))) {
                refundButton = button;
            }
        }
        assertNotNull("Couldn't find void refund button", refundButton);
        waitFor(By.id(refundButton.getAttribute("id")));
        refundButton.click();
        Alert alert = driver.switchTo().alert();
        alert.accept();
        waitFor(By.id("messages"));
        assertEquals(message, driver.findElement(By.id("messages")).getText());
    }

    void iPressCancel(String message) {
        WebElement cancelButton = null;
        List<WebElement> buttons = driver.findElement(By.id("content")).findElement(By.className("form-buttons")).findElements(By.tagName("button"));
        for(WebElement button : buttons) {
            if("Cancel".equals(button.getAttribute("title"))) {
                cancelButton = button;
            }
        }
        assertNotNull("Couldn't find cancel button", cancelButton);
        waitFor(By.id(cancelButton.getAttribute("id")));
        cancelButton.click();
        Alert alert = driver.switchTo().alert();
        alert.accept();
        waitFor(By.id("messages"));
        assertEquals(message, driver.findElement(By.id("messages")).getText());
    }

    void iPressAuthReversal(String message) {
        WebElement authReversalButton = null;
        List<WebElement> buttons = driver.findElement(By.id("content")).findElement(By.className("form-buttons")).findElements(By.tagName("button"));
        for(WebElement button : buttons) {
            if("Auth-Reversal".equals(button.getAttribute("title"))) {
                authReversalButton = button;
            }
        }
        assertNotNull("Couldn't find auth reversal button", authReversalButton);
        waitFor(By.id(authReversalButton.getAttribute("id")));
        authReversalButton.click();
        Alert alert = driver.switchTo().alert();
        alert.accept();
        waitFor(By.id("messages"));
        assertEquals(message, driver.findElement(By.id("messages")).getText());
    }

    void iClickOnInvoices() {
        WebElement invoiceLink = null;
        for(WebElement link : driver.findElement(By.id("sales_order_view_tabs")).findElements(By.tagName("a"))) {
            if("Order Invoices".equals(link.getAttribute("title"))) {
                invoiceLink = link;
            }
        }
        assertNotNull(invoiceLink);
        invoiceLink.click();
        waitFor(By.id("order_invoices"));
    }

    void iClickOnTheTopRowInInvoices() {
        String url = driver.findElement(By.xpath("/html/body/div[2]/div[3]/div/div/div[2]/div/div[3]/div[2]/div/div/div/table/tbody/tr")).getAttribute("title");
        driver.get(url);
        waitFor(By.id("comments_block"));
    }

    protected void iSelectTopOrders(int numOrdersToSelect) {
        WebElement e = driver.findElement(By.id("sales_order_grid_table"));
        e = e.findElement(By.tagName("tbody"));
        List<WebElement> rows = e.findElements(By.tagName("tr"));
        for(int i = 0; i < numOrdersToSelect; i++) {
            WebElement row = rows.get(i);
            WebElement checkbox = row.findElement(By.tagName("input"));
            checkbox.click();
        }
    }

    protected String getOrderNumForOrder(int orderRow) {
        WebElement e = driver.findElement(By.id("sales_order_grid_table"));
        e = e.findElement(By.tagName("tbody"));
        List<WebElement> rows = e.findElements(By.tagName("tr"));
        WebElement row = rows.get(orderRow);
        List<WebElement> cols = row.findElements(By.tagName("td"));
        WebElement col = cols.get(1);
        return col.getText().trim();
    }

    protected void iPressSubmitOnOrders() {
        WebElement e = driver.findElement(By.id("sales_order_grid_massaction-form"));
        e = e.findElement(By.tagName("button"));
        e.click();

        waitFor(By.id("messages"));
    }




}

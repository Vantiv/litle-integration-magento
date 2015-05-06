package com.litle.magento.selenium;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertNotNull;
import static org.junit.Assert.assertTrue;

import java.io.File;
import java.io.IOException;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.Statement;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.List;
import java.util.concurrent.TimeUnit;

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
import org.openqa.selenium.firefox.internal.ProfilesIni;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.interactions.PauseAction;
import org.openqa.selenium.support.events.AbstractWebDriverEventListener;
import org.openqa.selenium.support.events.EventFiringWebDriver;
import org.openqa.selenium.support.events.WebDriverEventListener;
import org.openqa.selenium.support.ui.ExpectedCondition;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

public class BaseTestCase {

    private static final String WORKSPACE = System.getenv("WORKSPACE") == null ? System.getProperty("user.home") : System.getenv("WORKSPACE");
    private static final String SCREENSHOT_DIR = WORKSPACE + "/test/screenshots/";
    private static final String HOST = System.getenv("MAGENTO_HOST");
    private static final String FIREFOX_PATH = System.getenv("FIREFOX_PATH");
    private static final String MAGENTO_DB_NAME = System.getenv("MAGENTO_DB_NAME");
    private static final String MAGENTO_DB_USER = System.getenv("MAGENTO_DB_USER");
    private static final String MAGENTO_DB_PASS = System.getenv("MAGENTO_DB_PASS");
    private static final String MAGENTO_HOME = System.getenv("MAGENTO_HOME");
    private static final String CONTEXT = System.getenv("MAGENTO_CONTEXT");
    private static final SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
    private static final long DEFAULT_TIMEOUT = 20;
    private static String JDBC_URL;
    private static Connection conn;
    static Statement stmt;
    static EventFiringWebDriver driver;
    static WebDriverWait wait;

    @BeforeClass
    public static void setupSuite() throws Exception {

        FileUtils.deleteDirectory(new File(MAGENTO_HOME+"/var/cache"));
        FileUtils.deleteDirectory(new File(MAGENTO_HOME+"/var/log"));
        FileUtils.deleteDirectory(new File(SCREENSHOT_DIR));
        JDBC_URL = "jdbc:mysql://localhost:3306/" + MAGENTO_DB_NAME;
        Class.forName("com.mysql.jdbc.Driver");
        conn = DriverManager.getConnection(JDBC_URL, MAGENTO_DB_USER, MAGENTO_DB_PASS);
        stmt = conn.createStatement();
        stmt.executeUpdate("delete from core_resource where code = 'palorus_setup'");
        stmt.executeUpdate("delete from core_resource where code = 'lecheck_setup'");
        stmt.executeUpdate("delete from core_resource where code = 'creditcard_setup'");
        stmt.executeUpdate("delete from core_resource where code = 'lpaypal_setup'");
        stmt.executeUpdate("delete from core_config_data where path like 'payment/CreditCard/%'");
        stmt.executeUpdate("delete from core_config_data where path like 'payment/LEcheck/%'");
        stmt.executeUpdate("delete from core_config_data where path like 'payment/LPaypal/%'");

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
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/vault_enable','0')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/paypage_url',null)");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/paypage_id',null)");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/timeout',null)");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/LEcheck/active','0')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/LEcheck/title',null)");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/LEcheck/payment_action','authorize')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/LEcheck/order_status','processing')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/proxy','iwp1.lowell.litle.com:8080')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/CreditCard/cctypes','AE,DC,VI,MC,DI,JCB')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/LPaypal/active','0')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/LPaypal/payment_action','authorize')");
        stmt.executeUpdate("INSERT INTO core_config_data (scope,scope_id,path,value) VALUES ('default',0,'payment/LPaypal/title',null)");
    }

    @Before
    public void before() throws Exception {
        System.setProperty("webdriver.firefox.bin",FIREFOX_PATH);
        ProfilesIni allProfiles = new ProfilesIni();
        FirefoxProfile profile = allProfiles.getProfile("Magento");
        profile.setEnableNativeEvents(true);
        driver = new EventFiringWebDriver(new FirefoxDriver(profile));
        wait = new WebDriverWait(driver, DEFAULT_TIMEOUT);
        WebDriverEventListener errorListener = new AbstractWebDriverEventListener() {
            @Override
            public void onException(Throwable throwable, WebDriver driver) {
                String testClass = "";
                String testMethod = "";
                for(StackTraceElement stackTrace : throwable.getStackTrace()) {
                    String className = stackTrace.getClassName();
                    if(className.endsWith("Tests") && className.startsWith("com.litle.magento.selenium")) {
                        testClass = className;
                        testMethod = stackTrace.getMethodName();
                    }
                }
                Calendar c = Calendar.getInstance();
                takeScreenshot(testClass + "." + testMethod + " " + sdf.format(c.getTime()) + "-" + driver.getTitle() + "-" + String.valueOf(System.currentTimeMillis()));
            }

            private void takeScreenshot(String screenshotName) {
                File tempFile = ((TakesScreenshot)driver).getScreenshotAs(OutputType.FILE);
                try {
                    FileUtils.copyFile(tempFile, new File(SCREENSHOT_DIR + screenshotName + ".png"));
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
        driver.quit();
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
    
    static void iAmDoingLPaypalTransaction() throws Exception {
        stmt.executeUpdate("update core_config_data set value='https://www.testlitle.com/sandbox/communicator/online' where path='payment/CreditCard/url'");
        stmt.executeUpdate("update core_config_data set value='JENKINS' where path='payment/CreditCard/user'");
        stmt.executeUpdate("update core_config_data set value='LPaypalTransactions' where path='payment/CreditCard/password'");
        stmt.executeUpdate("update core_config_data set value='(\"USD\"=>\"101\",\"GBP\"=>\"102\")' where path='payment/CreditCard/merchant_id'");
        stmt.executeUpdate("update core_config_data set value='iwp1.lowell.litle.com:8080' where path='payment/CreditCard/proxy'");

        // for Paypal
        stmt.executeUpdate("update core_config_data set value='1' where path='payment/paypal_express/active'");
//        stmt.executeUpdate("update core_config_data set value='0' where path='payment/paypal_express/skip_order_review_step'");
        stmt.executeUpdate("update core_config_data set value='Order' where path='payment/paypal_express/payment_action'");
        stmt.executeUpdate("update core_config_data set value='1' where path='paypal/wpp/sandbox_flag'");
        stmt.executeUpdate("update core_config_data set value='1' where path='payment/LPaypal/active'");
        stmt.executeUpdate("update core_config_data set value='Litle Paypal' where path='payment/LPaypal/title'");
        
        // change the flatrate shipping type
        stmt.executeUpdate("update core_config_data set value='O' where path='carriers/flatrate/type'");
        
        stmt.executeUpdate("delete from sales_flat_order");
        stmt.executeUpdate("delete from sales_flat_quote");
    }

    static void iAmDoingNonPaypageTransaction() throws Exception {
        stmt.executeUpdate("update core_config_data set value='0' where path='payment/CreditCard/paypage_enable'");
    }

    static void iAmDoingPaypageTransaction() throws Exception {
        stmt.executeUpdate("update core_config_data set value='1' where path='payment/CreditCard/paypage_enable'");
        stmt.executeUpdate("update core_config_data set value='a2y4o6m8k0' where path='payment/CreditCard/paypage_id'");
        stmt.executeUpdate("update core_config_data set value='https://request-prelive.np-securepaypage-litle.com' where path='payment/CreditCard/paypage_url'");
    }

    static void iAmDoingStoredCards() throws Exception {
        stmt.executeUpdate("update core_config_data set value='1' where path='payment/CreditCard/vault_enable'");
    }
    
    static void iAmDoingLitleAuth() throws Exception {
        stmt.executeUpdate("update core_config_data set value='authorize' where path='payment/CreditCard/payment_action'");
        stmt.executeUpdate("update core_config_data set value='authorize' where path='payment/LEcheck/payment_action'");
        stmt.executeUpdate("update core_config_data set value='authorize' where path='payment/LPaypal/payment_action'");
    }

    static void iAmDoingLitleSale() throws Exception {
        stmt.executeUpdate("update core_config_data set value='authorize_capture' where path='payment/CreditCard/payment_action'");
        stmt.executeUpdate("update core_config_data set value='authorize_capture' where path='payment/LEcheck/payment_action'");
        stmt.executeUpdate("update core_config_data set value='authorize_capture' where path='payment/LPaypal/payment_action'");
    }

    void iAmLoggedInAsWithThePassword(String username, String password) throws Exception {
        driver.get("http://"+HOST+"/" + CONTEXT + "/index.php/");
//        
//        if(driver.findElements(By.linkText("Log Out")).size() != 0){
//            driver.findElement(By.linkText("Log Out")).click();
//        }
        
        waitFor(By.linkText("Log In"));

        //Login
        driver.findElement(By.linkText("Log In")).click();
        driver.findElement(By.id("pass")).clear();
        driver.findElement(By.id("pass")).sendKeys(password);
        Thread.sleep(1000L);
        driver.findElement(By.id("email")).clear();
        driver.findElement(By.id("email")).sendKeys(username);
        Thread.sleep(1000L);
        driver.findElement(By.id("send2")).click(); //click login button
        waitForCssVisible("html body.customer-account-index div.wrapper div.page div.main-container div.main div.col-main div.my-account div.dashboard div.page-title h1");
    }

    WebElement waitForIdVisible(String id) {
        return wait.until(ExpectedConditions.visibilityOfElementLocated(By.id(id)));
    }

    WebElement waitForCssVisible(String css) {
        return wait.until(ExpectedConditions.visibilityOfElementLocated(By.cssSelector(css)));
    }

    WebElement waitForClassVisible(String className) {
        return wait.until(ExpectedConditions.visibilityOfElementLocated(By.className(className)));
    }

    WebElement waitFor(By locator) {
        return wait.until(ExpectedConditions.visibilityOfElementLocated(locator));
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

    void iClickOnTheTopRowInOrders() throws Exception {
        WebElement ordersGrid = driver.findElement(By.id("sales_order_grid_table"));
        WebElement topRow = ordersGrid.findElement(By.tagName("tbody")).findElement(By.tagName("tr"));
        //WebElement topRow = driver.findElement(By.xpath("/html/body/div/div[3]/div/div[3]/div/div[2]/div/table/tbody/tr[1]"));
        String title = topRow.getAttribute("title");
        driver.get(title);

        waitFor(By.className("head-billing-address"));
        waitFor(By.className("head-sales-order"));
    }

    void iClickOnTheTopRowInCustomerInsights() {
        WebElement table = driver.findElement(By.id("my_custom_tab_table"));
        WebElement tbody = table.findElement(By.tagName("tbody"));
        List<WebElement> rows = tbody.findElements(By.tagName("tr"));
        assertTrue(rows.size() > 0);
        WebElement topRow = rows.get(0);
        String title = topRow.getAttribute("title");
        driver.get(title);
        waitFor(By.id("sales_order_view"));
    }

    void iAddTheTopRowInProductsToTheOrder() {
//        WebElement topRow = driver.findElement(By.xpath("/html/body/div/div[3]/div/form/div[5]/div/div/table/tbody/tr/td[2]/div[2]/div/div[2]/div/div/div/table/tbody/tr"));
//        topRow.click();
//        waitFor(By.cssSelector("html body#html-body.adminhtml-sales-order-create-index div.wrapper div#anchor-content.middle div#page:main-container form#edit_form div#order-data div div.page-create-order table tbody tr td.main-col div#order-items div div.entry-edit div table tbody tr td.a-right button .scalable"));
        WebElement productsTable = driver.findElement(By.id("sales_order_create_search_grid_table"));
        productsTable.findElement(By.tagName("tbody")).findElement(By.tagName("tr")).click(); //Clicking the top row sets the qtyToAdd to 1
        WebElement e = driver.findElement(By.id("order-search"));
        e = e.findElement(By.className("entry-edit-head"));
        e = e.findElement(By.tagName("button"));
        e.click();
        waitForIdVisible("order-items");
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
        waitFor(By.linkText(subMenu));
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
        } else if("Transactions".equals(subMenu)) {
            waitFor(By.id("order_transactions"));
        }
    }


    void iAmLoggedInAsAnAdministrator() {
        //Get to login screen
        driver.get("http://"+HOST+"/" + CONTEXT + "/index.php/admin");
        waitForIdVisible("username");

        //Enter username
        WebElement e = driver.findElement(By.id("username"));
        e.clear();
        e.sendKeys("admin");

        //Enter password
        e = driver.findElement(By.id("login"));
        e.clear();
        e.sendKeys("LocalMagentoAdmin1");
        e.submit();

        waitForClassVisible("link-logout");
    }

    void iAccessAReportingUrl(String lastBitOfUrl) {
        driver.get("http://"+HOST+"/" + CONTEXT + "/index.php" + lastBitOfUrl);
        //Enter username
        WebElement e = driver.findElement(By.id("username"));
        e.clear();
        e.sendKeys("admin");

        //Enter password
        e = driver.findElement(By.id("login"));
        e.clear();
        e.sendKeys("LocalMagentoAdmin1");
        e.submit();

    }
    
    private void goThroughBillingAndShipping(){
        //And I press "Continue"
        WebElement e = driver.findElement(By.id("co-billing-form"));
        e = e.findElement(By.tagName("button"));
        e.click();
        waitForIdVisible("co-shipping-method-form");

        //        And I press the "3rd" continue button - Shipping Method
        e = driver.findElement(By.id("co-shipping-method-form"));
        e = e.findElement(By.tagName("button"));
        e.click();
        waitForIdVisible("co-payment-form");
    }

    private void baseCheckoutHelper(String ccType, String creditCardNumber, boolean saveCreditCard) {
        //And I press "Proceed to Checkout"
        WebElement e = driver.findElement(By.className("btn-proceed-checkout"));
        e.click();
        waitForIdVisible("co-billing-form");
        
        // go through Billing and Shipping sections
        goThroughBillingAndShipping();
        
        //And I choose "CreditCard"
        e = driver.findElement(By.id("p_method_creditcard"));
        e.click();
        waitForIdVisible("creditcard_cc_type");

        if(creditCardNumber.startsWith("Stored")) {
            iSelectFromSelect(creditCardNumber, "creditcard_cc_vaulted");
        }
        else {
            //And I select "Visa" from "Credit Card Type"
            iSelectFromSelect(ccType, "creditcard_cc_type");

            //        And I put in "Credit Card Number" with "4000162019882000"
            e = driver.findElement(By.id("creditcard_cc_number"));
            e.clear();
            e.sendKeys(creditCardNumber);
        }

        //        And I select "9" from "Expiration Date"
        iSelectFromSelect("09 - September", "creditcard_expiration");

        //        And I select "2012" from "creditcard_expiration_yr"
        iSelectFromSelect("2020", "creditcard_expiration_yr");

        //        And I put in "Card Verification Number" with "123"
        e = driver.findElement(By.id("creditcard_cc_cid"));
        e.clear();
        e.sendKeys("123");

        if(saveCreditCard) {
            e = driver.findElement(By.id("creditcard_cc_should_save"));
            e.click();
        }

        //        And I press the "4th" continue button
        e = driver.findElement(By.id("payment-buttons-container"));
        e = e.findElement(By.tagName("button"));
        e.click();
        waitForIdVisible("checkout-step-review");

        //        And I press "Place Order"
        e = driver.findElement(By.id("review-buttons-container"));
        e = e.findElement(By.tagName("button"));
        e.click();

    }
    
    private void loginPaypalAndConfirm(String account, String password){
        WebElement emailInput;
        WebElement passwdInput;
        WebElement loginButton;
        String comfirmButtonId;
        WebElement comfirmButton;
        String emailCssStr = "html body section.login div.inputField.emailField.confidential > input";
        String passCssStr = "html body section.login div.inputField.passwordField.confidential > input";
        // login into the paypal sandbox
        try {
            try {
                // for new paypal sandbox page with element id
                waitForIdVisible("email");
                emailInput = driver.findElement(By.id("email"));
                passwdInput = driver.findElement(By.id("password"));
                loginButton = driver.findElement(By.cssSelector(".btn.full.continue"));
            } catch (Exception e1){
                // for new paypal sandbox page without element id
                waitForCssVisible(emailCssStr);
                emailInput = driver.findElement(By.cssSelector(emailCssStr));
                passwdInput = driver.findElement(By.cssSelector(passCssStr));
                loginButton = driver.findElement(By.cssSelector(".btn.full.loginBtn"));
            }
            comfirmButtonId = "confirmButtonTop";
        } catch (Exception e2) {
            // for old paypal sandbox page
            waitForIdVisible("login_email");
            emailInput = driver.findElement(By.id("login_email"));
            passwdInput = driver.findElement(By.id("login_password"));
            loginButton = driver.findElement(By.id("submitLogin"));
            comfirmButtonId = "continue_abovefold";
        }
        emailInput.clear();
        emailInput.sendKeys(account);
        passwdInput.clear();
        passwdInput.sendKeys(password);
        loginButton.click();
        // click continue button on the review page
        waitForIdVisible(comfirmButtonId);
        comfirmButton = driver.findElement(By.id(comfirmButtonId));
        comfirmButton.click();
    }

    private void onepageLPaypalCheckoutHelper(String account, String password){
        // select the Paypal express checkout method
        WebElement e = driver.findElement(By.id("p_method_paypal_express"));
        e.click();
        waitForClassVisible("form-alt");
        
        //      And I press the "4th" continue button
        e = driver.findElement(By.id("payment-buttons-container"));
        e = e.findElement(By.tagName("button"));
        e.click();
        
        // login Paypal website and confirm the payment
        loginPaypalAndConfirm(account, password);
    }
    
    void iFailCheckOutWith(String ccType, String creditCardNumber, String modalErrorMessage) throws InterruptedException {
        baseCheckoutHelper(ccType, creditCardNumber, false);
        Thread.sleep(2000);
        Alert alert = driver.switchTo().alert();
        String alertText = alert.getText();
        assertTrue(alertText, alertText.contains(modalErrorMessage));
        alert.accept();
    }

    void iCheckOutWith(String ccType, String creditCardNumber) {
        iCheckOutWith(ccType, creditCardNumber, false);
    }

    void iCheckOutWith(String ccType, String creditCardNumber, boolean saveCreditCard) {
        baseCheckoutHelper(ccType, creditCardNumber, saveCreditCard);
        //	    Then I should see "Thank you for your purchase"
        waitForCssVisible("html body.checkout-onepage-success div.wrapper div.page div.main-container div.main div.col-main p a");
        WebElement e = driver.findElement(By.className("col-main"));
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
    
    void iCheckOutWithLPaypal(String account, String password) {
        //And I press "Proceed to Checkout"
        WebElement e = driver.findElement(By.className("btn-proceed-checkout"));
        e.click();
        waitForIdVisible("co-billing-form");
        
        // go through Billing and Shipping sections
        goThroughBillingAndShipping();
        
        // finish Paypal checkout flow
        onepageLPaypalCheckoutHelper(account, password);
        waitForIdVisible("review_button");
        // review and place order 
        reviewAndPlaceOrder();
        //      Then I should see "Thank you for your purchase"
        waitForCssVisible("html body.checkout-onepage-success div.wrapper div.page div.main-container div.main div.col-main p a");
        e = driver.findElement(By.className("col-main"));
        e = e.findElement(By.className("sub-title"));
        assertEquals("Thank you for your purchase!",e.getText());
    }

    void iCheckOutInCartWithLPaypal(String account, String password) {
        //And I press "Proceed to Checkout"
        WebElement e = driver.findElement(By.className("paypal-logo"));
        e = e.findElement(By.tagName("img"));
        e.click();
        
        
        // finish Paypal checkout flow
        loginPaypalAndConfirm(account, password);
        waitForIdVisible("review_button");
      
        // review and place order 
        reviewAndPlaceOrder();
        
//      Then I should see "Thank you for your purchase"
        waitForCssVisible("html body.checkout-onepage-success div.wrapper div.page div.main-container div.main div.col-main p a");
        e = driver.findElement(By.className("col-main"));
        e = e.findElement(By.className("sub-title"));
        assertEquals("Thank you for your purchase!",e.getText());
    }
    
    void reviewAndPlaceOrder(){
      WebElement e;
      String version = driver.getCurrentUrl();
      if (version.contains("1702") || version.contains("1810")){
          e = driver.findElement(By.id("shipping:telephone"));
          e.clear();
          e.sendKeys("1231231234");
          e = driver.findElement(By.id("update_order"));
          e.click();
          waitForPlaceOrderButtonEnable();
      }
     // And I select shipping method
      iSelectFromSelect("Fixed - $5.00", "shipping_method");
      waitForPlaceOrderButtonEnable();
    
      // And I press "Place Order"
      e = driver.findElement(By.id("review_button"));
      e.click();
    }
    
    void waitForPlaceOrderButtonEnable(){
        WebDriverWait wait = new WebDriverWait(driver,30);
        String version = driver.getCurrentUrl();
        if (version.contains("1702") || version.contains("1810")){
            wait.until(new ExpectedCondition<Boolean>() {
                public Boolean apply(WebDriver driver) {
                    WebElement button = driver.findElement(By.id("review_button"));
                    String enabled = button.getAttribute("class");
                    if(enabled.equals("button btn-checkout validation-passed")) 
                        return true;
                    else
                        return false;
                }
            });
        } else {
            wait.until(new ExpectedCondition<Boolean>() {
                public Boolean apply(WebDriver driver) {
                    WebElement button = driver.findElement(By.id("review_button"));
                    String enabled = button.getAttribute("class");
                    if(enabled.equals("button btn-checkout")) 
                        return true;
                    else
                        return false;
                }
            });
        }
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

    void iSelectNameFromSelect(String optionText, String selectName) {
        WebElement select = driver.findElement(By.name(selectName));
        List<WebElement> options = select.findElements(By.tagName("option"));
        for(WebElement option : options){
            if(option.getText().equals(optionText)) {
                option.click();
                break;
            }
        }
    }
    
    void iSelectFirstOption(String selectId) {
        WebElement select = driver.findElement(By.id(selectId));
        List<WebElement> options = select.findElements(By.tagName("option"));
        options.get(1).click();
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

    protected void iPressSubmitOrder() {
        List<WebElement> buttons = driver.findElements(By.tagName("button"));
        WebElement submitOrderButton = null;
        for(WebElement button : buttons) {
            if("Submit Order".equals(button.getAttribute("title"))) {
                submitOrderButton = button;
                break;
            }
        }
        assertNotNull("Couldn't find submit order button", submitOrderButton);
        submitOrderButton.click();
        waitForIdVisible("messages");
    }
    
        void iHaveMultipleProductsInMyCart(String productName,String numberOfProducts) {
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

        e = driver.findElement(By.cssSelector(".qty"));
        e.clear();
        e.sendKeys(numberOfProducts);

        e = driver.findElement(By.cssSelector(".btn-update"));
        e.click();

    }
}

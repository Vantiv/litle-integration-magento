<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\Exception,
    Behat\Behat\Exception\PendingException;

use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\Mink\Exception\ResponseTextException;

/**
 * Features context.
 */
class FeatureContext extends Behat\Mink\Behat\Context\MinkContext
{
	/**
	* @BeforeSuite
	*/
	public static function setupSuite(Behat\Behat\Event\SuiteEvent $event)
	{		
		$dbName = getenv('MAGENTO_DB_NAME');
		$dbUser = getenv('MAGENTO_DB_USER');
		$magentoHome = getenv('MAGENTO_HOME');
		system("mysql -u $dbUser $dbName < " . dirname(__FILE__) . "/setupSuite.sql");
		system("rm -rf $magentoHome/var/cache/*");		
	}
	
	/** @AfterStep */
	public function after(Behat\Behat\Event\StepEvent $event)
	{
		if($event->getResult() == 4) { //Failure
			$dbName = getenv('MAGENTO_DB_NAME');
			$dbUser = getenv('MAGENTO_DB_USER');
			$sql = <<<EOD
mysql -u $dbUser $dbUser -e "select path,value from core_config_data where path like 'payment/CreditCard/%'"
EOD;
			system($sql);				
			$sql = <<<EOD
mysql -u $dbUser $dbName -e "select path,value from core_config_data where path like 'payment/LEcheck/%'"
EOD;
			system($sql);				
		}
		
	}
	
	/**
	* @Given /^I am using the sandbox$/
	*/
	public function iAmUsingTheSandbox()
	{
		$dbName = getenv('MAGENTO_DB_NAME');
		$dbUser = getenv('MAGENTO_DB_USER');
		system("mysql -u $dbUser $dbName < " . dirname(__FILE__) . "/setupSandbox.sql");
	}
	
	/**
	* @Given /^I am doing paypage transaction$/
	*/
	public function iAmDoingPaypageTransaction()
	{
		$dbName = getenv('MAGENTO_DB_NAME');
		$dbUser = getenv('MAGENTO_DB_USER');
		system("mysql -u $dbUser $dbName < " . dirname(__FILE__) . "/enablePayPageTransaction.sql");
	}
	
	/**
	* @Given /^I am doing cc or echeck transactions$/
	*/
	public function iAmDoingCCOrEcheckTransaction()
	{
		$dbName = getenv('MAGENTO_DB_NAME');
		$dbUser = getenv('MAGENTO_DB_USER');
		system("mysql -u $dbUser $dbName < " . dirname(__FILE__) . "/setupCCandEcheck.sql");
	}
	
	/**
	* @Given /^I am doing non paypage transactions$/
	*/
	public function iAmDoingNonPaypageTransaction()
	{
		$dbName = getenv('MAGENTO_DB_NAME');
		$dbUser = getenv('MAGENTO_DB_USER');
		system("mysql -u $dbUser $dbName < " . dirname(__FILE__) . "/disablePayPageTransaction.sql");
	}
	
	/**
	* @Given /^I am doing Litle auth$/
	*/
	public function iAmDoingLitleAuth()
	{
		$dbName = getenv('MAGENTO_DB_NAME');
		$dbUser = getenv('MAGENTO_DB_USER');
		system("mysql -u $dbUser $dbName < " . dirname(__FILE__) . "/setupLitleForAuth.sql");
	}
	
	/**
	* @Given /^I am doing Litle sale$/
	*/
	public function iAmDoingLitleSale()
	{
		$dbName = getenv('MAGENTO_DB_NAME');
		$dbUser = getenv('MAGENTO_DB_USER');
		system("mysql -u $dbUser $dbName < " . dirname(__FILE__) . "/setupLitleForSale.sql");
	}
	
	/**
	* @Given /^I am doing stored creditcard transaction$/
	*/
	public function iAmDoingStoredCreditcardTransaction()
	{
		$dbName = getenv('MAGENTO_DB_NAME');
		$dbUser = getenv('MAGENTO_DB_USER');
		system("mysql -u $dbUser $dbName < " . dirname(__FILE__) . "/setupLitleForStoredCreditcard.sql");
	}
	
	/**
	* @Given /^I am doing paypage Sale transaction tests$/
	*/
	public function iAmDoingPaypageSaleTransactionTests()
	{
		$dbName = getenv('MAGENTO_DB_NAME');
		$dbUser = getenv('MAGENTO_DB_USER');
		system("mysql -u $dbUser $dbName < " . dirname(__FILE__) . "/setupPayPageSaleTransactionTest.sql");
	}
	
	/**
	* @Given /^I am using local vap$/
	*/
	public function iAmUsingLocalVap()
	{
		$dbName = getenv('MAGENTO_DB_NAME');
		$dbUser = getenv('MAGENTO_DB_USER');
		system("mysql -u $dbUser $dbName < " . dirname(__FILE__) . "/setupVap.sql");
	}
	
	
	/**
	* @Given /^There are no rows in the database table "([^"]*)"$/
	*/
	public function thereAreNoRowsInTheDatabaseTable($tableName)
	{
		$dbName = getenv('MAGENTO_DB_NAME');
		$dbUser = getenv('MAGENTO_DB_USER');
		$mysql = "mysql -u $dbUser $dbName -e 'delete from $tableName' &> /dev/null";
		system($mysql);
	}
	
	
    /**
     * @Given /^I am logged in as "([^"]*)" with the password "([^"]*)"$/
     */
    public function iAmLoggedInAsWithThePassword($username, $password)
    {
    	$magentoHome = getenv('MAGENTO_HOME');
    	$magentoHome_tmp = explode('/',$magentoHome);
    	$magentoHome = $magentoHome_tmp[count($magentoHome_tmp) - 1];
    	
		$session = $this->getMink()->getSession('sahi'); 

 		$session->visit('http://localhost/' . $magentoHome . '/index.php/');
	
// 		//Get to login screen
 		$page = $session->getPage();
 		$loginLink = $page->findLink("Log In");
 		if($loginLink == NULL) {
			throw new Exception("Could not find login link"); 			
 		}
 		$loginLink->click();
	
// 		//Login 
 		$page->findField("Email Address")->setValue($username);
 		//$page->findField("Password")->setValue($password);
 		$script = 'document.getElementById("pass").value=' . '"' . $password . '"';
 		$session->executeScript($script);
 		$page->findButton("Login")->click();		
    }

    /**
     * @Given /^I have "([^"]*)" in my cart$/
     */
    public function iHaveInMyCart($product)
    {      	    	
    	$session = $this->getMink()->getSession('sahi');
    	
    	//Find the item
     	$page = $session->getPage();
     	//$page->findField("search")->setValue($product);
     	$script = 'document.getElementById("search").value=' . '"' . $product . '"';
     	$session->executeScript($script);
     	$page->findButton("Search")->click();
     	
     	//Add to cart
     	$page->findButton("Add to Cart")->click();     	     
    }

    /**
     * @When /^I press "([^"]*)" at "([^"]*)"$/
     */
    public function iPressAt($button, $parentDiv)
    {
    	$session = $this->getMink()->getSession('sahi');
    	 
    	//Find the item
    	$page = $session->getPage();
    	$parent = $page->findById($parentDiv);
    	$parent->findButton($button)->click();
    }
    
    
    /**
    * @Given /^I sleep for (\d+) milliseconds$/
    */
    public function iSleepForMilliseconds($time)
    {
    	$session = $this->getMink()->getSession('sahi');
    	$session->wait($time);
    }
    
    /**
    * @Given /^I sleep for "([^"]*)" milliseconds$/
    */
    public function iSleepForMilliseconds2($time)
    {
    	$session = $this->getMink()->getSession('sahi');
    	$session->wait($time);
   	}

   	/**
   	* @Then /^I wait for the payments to appear$/
   	*/
   	public function iWaitForThePaymentsToAppear()
   	{
   		$this->getSession()->wait(5000,
   	        "(document.getElementById('p_method_creditcard') != null)"
   		);
   	}
    
    /**
    * @Given /^I choose "([^"]*)"$/
    */
    public function iChoose($choice)
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	if($choice === 'CreditCard') {
    		$page->findById("p_method_creditcard")->click();
    	}
    	if($choice === 'LEcheck') {
    		$page->findById("p_method_lecheck")->click();
    	}
    	if($choice === 'English') {
    		if(getenv('USER') === 'aagarwal') { //TODO Only Archit has this option, and we aren't sure how he got it
    			$page->findById("store_1")->click();
    		}
    	}
    	if($choice === 'Fixed Shipping') {
			$page->findById("s_method_flatrate_flatrate")->click();
    	}
    }
    
    /**
    * @Given /^I select "([^"]*)" from the dropbox "([^"]*)"$/
    */
    public function iSelectFromTheDropbox($choice, $dropbox)
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	$select = $page->findById($dropbox);
    	$select->selectOption($choice);
    }
    
    /**
    * @Given /^I press the "([^"]*)" continue button$/
    */
    public function iPressTheRdContinue($times)
    {
    	if($times == "3rd") {
    		$session = $this->getMink()->getSession('sahi');
    		$page = $session->getPage();
    		$fieldElements = $page->findAll('named',array('field', 'id|name|value|label'));
    		$elementsByCss = $page->findAll('css', "button");
    		$elementsByCss[3]->click();
    	}
        else if($times == "4th") {
    		$session = $this->getMink()->getSession('sahi');
    		$page = $session->getPage();
    		$fieldElements = $page->findAll('named',array('field', 'id|name|value|label'));
    		$elementsByCss = $page->findAll('css', "button");
    		$elementsByCss[4]->click();
      	}
    	else {
    		throw new PendingException();
    	}
    }
    
    
    /**
    * @Given /^I am logged in as an administrator$/
    */
    public function iAmLoggedInAsAnAdministrator()
    {
    	$session = $this->getMink()->getSession('sahi'); 

 		$session->visit(getenv('MAGENTO_URL_ADMIN'));
	
 		//Get to login screen
 		$page = $session->getPage(); 		
 		$page->findField("User Name:")->setValue("admin");
 		//$page->findField("Password:")->setValue("LocalMagentoAdmin1");
 		$session->executeScript('document.getElementById("login").value="LocalMagentoAdmin1"');
 		$page->findButton("Login")->click();    	
    }
    
    /**
     * @When /^I view "([^"]*)" "([^"]*)"$/
     */
    public function iView($menu1, $menu2)
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	
    	$page->findLink($menu1)->mouseOver();
    	$page->findLink($menu2)->click();
    }
    
    /**
    * @When /^I view "([^"]*)" "([^"]*)" "([^"]*)"$/
    */
    public function iView2($menu1, $menu2, $menu3)
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	
    	$page->findLink($menu1)->mouseOver();
    	$page->findLink($menu2)->mouseOver();
    	$page->findLink($menu3)->click();
    }
    
    /**
    * @Given /^I click on the top row in Transactions$/
    */
    public function iClickOnTheTopRowInTransactions()
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	 
    	$topRow = $session->getDriver()->find('/html/body/div/div[3]/div/div[3]/div/div/div/table/tbody/tr[1]');
    	$session->visit($topRow[0]->getAttribute("title"));
    }
    
    /**
    * @Given /^I click on the Invoice button on top$/
    */
    public function iClickOnTheInvoiceButtonOnTop()
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	$fieldElements = $page->findAll('named',array('field', 'id|name|value|label'));
    	$elementsByCss = $page->findAll('css', "button");
    	$elementsByCss[3]->click();
    }
    
    /**
    * @Given /^I click on the Transaction ID link$/
    */
    public function iClickOnTheTransactionIdLink()
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	 
    	$tmp = $session->getDriver()->find("/html/body/div/div[3]/div/div[3]/div/div[2]/table/tbody/tr/td/a[1]");
    	$link = $tmp[0];
    	$link->click();
    }
    
    /**
    * @Given /^I click on the Update Items And Qtys Button$/
    */
    public function iClickOnTheUpdateItemsAndQtysButton()
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	
    	$tmp = $session->getDriver()->find("/html/body/div[2]/div[3]/div/form/div[5]/div/div/table/tbody/tr/td[2]/div[2]/div/div/div[2]/table/tbody/tr/td[2]/button");
    	$link = $tmp[0];
    	$link->click();
    }
    
    /**
    * @Then /^I should see "([^"]*)" in the "([^"]*)"$/
    */
    public function iShouldSeeInThe($specific, $section)
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	 
    	$parent = $page->findById($section);
    	if($parent === NULL) {
    		throw new Exception("Could not find section with the id $section");
    	}
    	$text = $parent->getText();
    	if(preg_match("/.*" . $specific . ".*/", $text) == 0) {
    		throw new ResponseTextException("Could not find $specific in $section", $session);
    	}
    }
    
    /**
    * @Given /^I click on the customer "([^"]*)" in "([^"]*)"$/
    */
    public function iClickOnTheCustomerIn($expectedName, $location)
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	
    	$rowToClick = NULL;
    	if($location==='Manage Customers') {
	    	$rows = $session->getDriver()->find('/html/body/div/div[3]/div/div[3]/div/div[2]/div/table/tbody/tr');
	    	for($i = 1; $i <= count($rows); $i++) {
	    		$row = $session->getDriver()->find("/html/body/div/div[3]/div/div[3]/div/div[2]/div/table/tbody/tr[$i]/td[3]");
				$actualName = $row[0]->getText();
				if($expectedName === $actualName) {
					$rowToClick = $row[0];
				}
	    	}
    	}
    	else if($location === 'Create New Order') {
    		$rows = $session->getDriver()->find('/html/body/div/div[3]/div/form/div[3]/div/div[2]/div/div/div/table/tbody/tr');
    	   	for($i = 1; $i <= count($rows); $i++) {
	    		$row = $session->getDriver()->find("/html/body/div/div[3]/div/form/div[3]/div/div[2]/div/div/div/table/tbody/tr[$i]/td[2]");
				$actualName = $row[0]->getText();
				if($expectedName === $actualName) {
					$rowToClick = $row[0];
				}
	    	}
    	}
    	else {
    		throw new Exception ("Don't know how to find customers for the location " . $location);
    	}
    	
    	if($rowToClick !== NULL) {
    		$rowToClick->click();
    	}
    	else {
    		throw new Exception("Could not find customer named " . $expectedName);
    	}
    	
    	
    }
    
    /**
    * @Given /^I click on the product "([^"]*)"$/
    */
    public function iClickOnTheProduct($expectedName)
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	 
    	$rows = $session->getDriver()->find('/html/body/div/div[3]/div/form/div[5]/div/div/table/tbody/tr/td[2]/div/div/div[2]/div/div/div/table/tbody/tr');
    	$rowToClick = NULL;
    	for($i = 1; $i <= count($rows); $i++) {
    		$row = $session->getDriver()->find("/html/body/div/div[3]/div/form/div[5]/div/div/table/tbody/tr/td[2]/div/div/div[2]/div/div/div/table/tbody/tr[$i]/td[2]");
    		$actualName = $row[0]->getText();
    		if( preg_match("/.*?".$expectedName.".*?/", $actualName) ) {
    			$rowToClick = $row[0];
    		}
    	}
    	if($rowToClick !== NULL) {
    		$rowToClick->click();
    	}
    	else {
    		throw new Exception("Could not find product named " . $expectedName);
    	}
    }
    
    /**
    * @Given /^I click on the top row in Product Table$/
    */
    public function iClickOnTheTopRowInProductTable()
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    
    	$topRow = $session->getDriver()->find('/html/body/div/div[3]/div/form/div[5]/div/div/table/tbody/tr/td[2]/div/div/div[2]/div/div/div/table/tbody/tr/td[2]');
    	$topRow[0]->click();
    }
    
    /**
    * @Given /^I click on the top row in Orders$/
    */
    public function iClickOnTheTopRowInOrders()
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    
    	$topRow = $session->getDriver()->find('/html/body/div/div[3]/div/div[3]/div/div[2]/div/table/tbody/tr[1]');
    	$session->visit($topRow[0]->getAttribute("title"));
    }
    
    /**
    * @Given /^I click on Invoices$/
    */
    public function iClickOnInvoices()
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	
    	$tmp = $session->getDriver()->find("/html/body/div[2]/div[3]/div/div/div/ul/li[2]/a/span");
    	$link = $tmp[0];
    	$link->click();
    }    
    
    /**
    * @Given /^I click on the top row in Invoices$/
    */
    public function iClickOnTheTopRowInInvoices()
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    
    	$topRow = $session->getDriver()->find('/html/body/div[2]/div[3]/div/div/div[2]/div/div[3]/div[2]/div/div/div/table/tbody/tr');
    	$session->visit($topRow[0]->getAttribute("title"));
    }
    
    /**
    * @Given /^I click on refund$/
    */
    public function iClickOnRefund()
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	 
    	$tmp = $session->getDriver()->find('/html/body/div/div[3]/div/form/div[12]/div[6]/div[2]/div/button[2]');
    	$tmp[0]->click();
    }
    
    
    /**
     * @Then /^I should see "([^"]*)" in the column "([^"]*)"$/
     */
    public function iShouldSeeInTheColumn($expectedText, $columnName)
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	 
    	$element = $page->findById($columnName . "_1");
    	if($element == NULL) {
    		throw new Exception ("Column not found");
    	}
    	$value = $element->getText();
    	if($value !== $expectedText) {
    		throw new ResponseTextException("Could not find $expectedText in $columnName  Instead found $value", $session);
    	}
    }
    
    /**
    * @Then /^I should see "([^"]*)" in the column "([^"]*)" on the "([^"]*)" screen$/
    */
    public function iShouldSeeInTheColumnOnTheScreen($expectedText, $columnName, $screen)
    {    	
        $session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	
		$screenFound = false;
		$columnFound = false;
		$valueFound = false;
		$actualValueFound = NULL;
    	if($screen === 'Customer Insight') {
    		$screenFound = true;
    		$columns = $session->getDriver()->find('/html/body/div/div[3]/div/div/div[2]/div/div[3]/form/div[3]/div/div/div/table/thead/tr/th');
    		for($i = 1; $i <= count($columns); $i++) {
    			$column = $session->getDriver()->find("/html/body/div/div[3]/div/div/div[2]/div/div[3]/form/div[3]/div/div/div/table/thead/tr/th[$i]");    			
    			$actualColumnName = $column[0]->getText();
    			if($actualColumnName === $columnName) {
    				$columnFound = true;
    				$rowColumn = $session->getDriver()->find("/html/body/div/div[3]/div/div/div[2]/div/div[3]/form/div[3]/div/div/div/table/tbody/tr/td[$i]");
    				$actualValueFound = $rowColumn[0]->getText();
    				if($actualValueFound === $expectedText) {
    					$valueFound = true;
    				}
    			}
    		}
    	}
    	if(!$screenFound) {
    		throw new Exception ("Don't know how to find for the screen $screen");
    	}
    	else if(!$columnFound) {
    		throw new Exception ("Could not find for column $columnName for the screen $screen");
    	}
    	else if(!$valueFound) {
    		throw new Exception("Wrong value found for column $columnName for the screen $screen.  Expected $expectedText.  Got $actualValueFound");
    	}
    }
    
    /**
     * @Given /^I click on the top row in Customer Insight$/
     */
    public function iClickOnTheTopRowInCustomerInsight()
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	 
    	$topRow = $session->getDriver()->find('/html/body/div/div[3]/div/div/div[2]/div/div[3]/form/div[3]/div/div/div/table/tbody/tr[1]');
    	$title = $topRow[0]->getAttribute("title");
    	if($title == NULL) {
    		throw new ResponseTextException("Could not find an attribute title for top row", $session);
    	}
    	$session->visit($topRow[0]->getAttribute("title"));
    }
    
    /**
    * @Given /^the "([^"]*)" table should have a row with "([^"]*)" in the "([^"]*)" column$/
    */
    public function theTableShouldHaveARowWithInTheColumn($table, $expectedValue, $column)
    {
    	$dbName = getenv('MAGENTO_DB_NAME');
    	$dbUser = getenv('MAGENTO_DB_USER');
    	$request = "mysql -u $dbUser $dbName -e \"select count(*) from $table where $column = '$expectedValue'\"";
    	$response = exec($request);
    	if($response !== '1') {
    		throw new Exception("Table did not have expected value");
    	}    	
    }
    
    /**
    * @Given /^the "([^"]*)" table should have a row like "([^"]*)" in the "([^"]*)" column$/
    */
    public function theTableShouldHaveARowLikeInTheColumn($table, $expectedValue, $column)
    {
    	$dbName = getenv('MAGENTO_DB_NAME');
    	$dbUser = getenv('MAGENTO_DB_USER');
    	$request = "mysql -u $dbUser $dbName -e \"select count(*) from $table where $column like '$expectedValue'\"";
    	$response = exec($request);
    	if($response !== '1') {
    		throw new Exception("Table did not have expected value");
    	}
    }
    
    
    /**
    * @Given /^the "([^"]*)" should have "([^"]*)" rows$/
    */
    public function theShouldHaveRows($table, $expectedRows)
    {
    	$dbName = getenv('MAGENTO_DB_NAME');
    	$dbUser = getenv('MAGENTO_DB_USER');
    	$request = "mysql -u $dbUser $dbName -e 'select count(*) from $table'";
    	$response = exec($request);
    	if($response !== $expectedRows) {
    		throw new Exception("Table did not have expected number of rows.  Found $response rows");
    	}    	    	
    }
    
    /**
    * @Given /^I put in "([^"]*)" with "([^"]*)"$/
    */
    public function iPutInWith($name, $value)
    {
    $session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	if($name === 'Credit Card Number') {
    		$script = 'document.getElementById("creditcard_cc_number").value=' . '"' . $value . '"';
    		$session->executeScript($script);
    	}
    	
    	if($name == 'Card Verification Number'){
    		$script = 'document.getElementById("creditcard_cc_cid").value=' . '"' . $value . '"';
    		$session->executeScript($script);
    	}
    	
    }
    
    /**
    * @Then /^I should not see "([^"]*)" in Credit Card Number$/
    */
    public function iShouldNotSeeInCreditCardNumber($number)
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    
    	//$parent = $page->findById($section);
    	$parent = $session->getDriver()->find('/html/body/div[2]/div[3]/div/div/div[2]/div/div[3]/div/div/div[8]/div/fieldset/table/tbody/tr[2]/td[2]');
    	$text = $parent[0]->getText();
    	
    	if(preg_match("/.*" . "xxxx-" . ".*/", $text) == 0) {
    		throw new ResponseTextException("Could not find a credit card number", $session);
    	}
    	if (preg_match("/.*" . "xxxx-" . $number . ".*/", $text) !== 0){
    		throw new ResponseTextException("Credit card number was not updated", $session);
    	}
    }
    
    /**
    * @Given /^I press the "([^"]*)" button$/
    */
    public function iPressTheButton($span)
    {
    	$session = $this->getMink()->getSession('sahi');
    	$script = <<<EOD
$$("button > span:contains('$span')")[0].parentNode.click()
EOD;
    	$session->executeScript($script);
    }
    
    
}

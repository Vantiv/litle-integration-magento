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
	
	/**
	* @Given /^I am using the sandbox$/
	*/
	public function iAmUsingTheSandbox()
	{
		$dbName = getenv('MAGENTO_DB_NAME');
		$dbUser = getenv('MAGENTO_DB_USER');
		$magentoHome = getenv('MAGENTO_HOME');
		system("mysql -u $dbUser $dbName < " . dirname(__FILE__) . "/setupSandbox.sql");
	}
	
	/**
	* @Given /^I am using local vap$/
	*/
	public function iAmUsingLocalVap()
	{
		$dbName = getenv('MAGENTO_DB_NAME');
		$dbUser = getenv('MAGENTO_DB_USER');
		$magentoHome = getenv('MAGENTO_HOME');
		system("mysql -u $dbUser $dbName < " . dirname(__FILE__) . "/setupVap.sql");
	}
	
	
	/**
	* @Given /^There are no rows in the database table "([^"]*)"$/
	*/
	public function thereAreNoRowsInTheDatabaseTable($tableName)
	{
		$dbName = getenv('MAGENTO_DB_NAME');
		$dbUser = getenv('MAGENTO_DB_USER');
		$magentoHome = getenv('MAGENTO_HOME');
		$mysql = "mysql -u $dbUser $dbName -e 'delete from $tableName' &> /dev/null";
		system($mysql);
	}
	
	
    /**
     * @Given /^I am logged in as "([^"]*)" with the password "([^"]*)"$/
     */
    public function iAmLoggedInAsWithThePassword($username, $password)
    {
		$session = $this->getMink()->getSession('sahi'); 

 		$session->visit('http://localhost/magento/index.php/');
	
// 		//Get to login screen
 		$page = $session->getPage();
 		$loginLink = $page->findLink("Log In");
 		if($loginLink == NULL) {
			throw new Exception("Could not find login link"); 			
 		}
 		$loginLink->click();
	
// 		//Login 
 		$page->findField("Email Address")->setValue($username);
 		$page->findField("Password")->setValue($password);
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
     	$page->findField("search")->setValue($product);
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
    * @Given /^I choose "([^"]*)"$/
    */
    public function iChoose($choice)
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	if($choice === 'CreditCard') {
    		$page->findById("p_method_creditcard")->click();
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

 		$session->visit('http://127.0.0.1/magento/index.php/admin/');
	
 		//Get to login screen
 		$page = $session->getPage(); 		
 		$page->findField("User Name:")->setValue("admin");
 		$page->findField("Password:")->setValue("LocalMagentoAdmin1");
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
    * @Then /^I should see "([^"]*)" in the "([^"]*)"$/
    */
    public function iShouldSeeInThe($specific, $section)
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	 
    	$parent = $page->findById($section);
    	$text = $parent->getText();
    	if(preg_match("/.*" . $specific . ".*/", $text) == 0) {
    		throw new ResponseTextException("Could not find $specific in $section", $session);
    	}
    }
    
    /**
    * @Given /^I click on the top row in Customers$/
    */
    public function iClickOnTheTopRowInCustomers()
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	 
    	$topRow = $session->getDriver()->find('/html/body/div/div[3]/div/div[3]/div/div[2]/div/table/tbody/tr[1]');
    	$session->visit($topRow[0]->getAttribute("title"));
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
     * @Given /^I click on the top row in Customer Insight$/
     */
    public function iClickOnTheTopRowInCustomerInsight()
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	 
    	$topRow = $session->getDriver()->find('/html/body/div/div[3]/div/div/div[2]/div/div[3]/form/div[3]/div/table/tbody/tr[1]');
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
    	$magentoHome = getenv('MAGENTO_HOME');
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
    	$magentoHome = getenv('MAGENTO_HOME');
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
    	$magentoHome = getenv('MAGENTO_HOME');
    	$request = "mysql -u $dbUser $dbName -e 'select count(*) from $table'";
    	$response = exec($request);
    	if($response !== $expectedRows) {
    		throw new Exception("Table did not have expected number of rows.  Found $response rows");
    	}    	    	
    }
    
    
}

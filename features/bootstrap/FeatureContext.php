<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;

use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

/**
 * Features context.
 */
class FeatureContext extends Behat\Mink\Behat\Context\MinkContext
{
	
	/**
	* @BeforeFeature
	*/
	public static function setupFeature(Behat\Behat\Event\FeatureEvent $event)
	{
		$featureName = $event->getFeature()->getTitle();
		switch($featureName) {
			case "TransactionDetail":
				system("mysql -u magento magento < " . dirname(__FILE__) . "/setupTransactionDetail.sql");
				break;
			case "CustomerInformation":
				system("mysql -u magento magento < " . dirname(__FILE__) . "/setupCustomerInformation.sql");
				break;
		}
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
    
    
}

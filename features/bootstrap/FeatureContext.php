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
     * @Then /^I wait for the suggestion box to appear$/
     */
    public function iWaitForTheSuggestionBoxToAppear()
    {
        $this->getSession()->wait(5000, "$('.suggestions-results').children().length > 0");
    }

    /**
     * @Given /^I am logged in as "([^"]*)" with the password "([^"]*)"$/
     */
    public function iAmLoggedInAsWithThePassword($username, $password)
    {
		//$driver = new \Behat\Mink\Driver\SahiDriver('firefox');
 		//$session = new \Behat\Mink\Session($driver);
		$session = $this->getMink()->getSession('sahi');    	//var_dump($session);

// 		// start session:
// 		$session->start();
 		$session->visit('http://localhost/magento/index.php/');
	
// 		//Get to login screen
 		$page = $session->getPage();
 		$loginLink = $page->findLink("Log In");
 		$loginLink->click();
	
// 		//Login 
 		//$page = $session->getPage();
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
     	
     	//Proceed to checkout
      	//$page->findButton("Proceed to Checkout")->click();
     	
//     	$session->wait(5000);    	 
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
    	
    	
    	//echo $page->getContent();
    	//$page->findButton($button)->click();
    	    	
    	//$session->wait(2000);        
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
    * @Given /^I execute the javascript method "([^"]*)"$/
    */
    public function iExecuteTheJavascriptMethod($script)
    {
    	$session = $this->getMink()->getSession('sahi');
    	$session->evaluateScript($script);
    }
    
    /**
    * @Given /^I choose "([^"]*)" from "([^"]*)"$/
    */
    public function iChooseFrom($choice, $parent)
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	//$parent = $page->findById($parent);
    	$page->findById($choice)->click();    	 
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
    * @When /^I press "([^"]*)" "([^"]*)"$/
    */
    public function iPress($button, $times)
    {
    	$session = $this->getMink()->getSession('sahi');
    	$page = $session->getPage();
    	$fieldElements = $page->findAll('named',
    	array('field', 'id|name|value|label')
    	);
    	$elementsByCss = $page->findAll('css', $button);
    	var_dump($elementsByCss);
    	$elementsByCss[intval($times)]->click();
    	
    }
    
    
}

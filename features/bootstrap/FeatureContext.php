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
	$driver = new \Behat\Mink\Driver\SahiDriver('firefox');
	$session = new \Behat\Mink\Session($driver);

	// start session:
	$session->start();
	$session->visit('http://localhost/magento/index.php/');

	//Get to login screen
	$page = $session->getPage();
	$loginLink = $page->findLink("Log In");
	$loginLink->click();

	//Login 
	$page = $session->getPage();
	$page->findField("Email Address")->setValue($username);
	$page->findField("Password")->setValue($password);
	$page->findButton("Login")->click();
	
        throw new PendingException();
    }

    /**
     * @Given /^I have "([^"]*)" in my cart$/
     */
    public function iHaveInMyCart($argument1)
    {
        throw new PendingException();
    }

    /**
     * @When /^I press "([^"]*)" at "([^"]*)"$/
     */
    public function iPressAt($argument1, $argument2)
    {
        throw new PendingException();
    }

}

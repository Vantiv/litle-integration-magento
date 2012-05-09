Feature: TransactionDetail
  In order to see a link to the litle payment detail screen on the magento transaction detail page
  As a user
  I need to be able to purchase something
  And As an admin
  I need to be able to see the link
  
  Background:
    And I am using local vap

  
  @javascript @ready @creditcard
  Scenario: Buying an item with a visa credit card
    Given I am logged in as "gdake@litle.com" with the password "password"
      And I am doing Litle auth
    When I have "vault" in my cart
      And I press "Proceed to Checkout"
      And I press "Continue"
      And I press the "3rd" continue button
      And I choose "CreditCard"
      And I select "Visa" from "Credit Card Type"
      And I fill in "Credit Card Number" with "4100000000000001"
      And I select "1" from "Expiration Date"
      And I select "2017" from "creditcard_expiration_yr"
      And I fill in "Card Verification Number" with "123"
      And I press the "4th" continue button
      And I press "Place Order"
    Then I should see "Thank you for your purchase"
      And I follow "Log Out"
    Given I am logged in as an administrator
    When I view "Sales" "Transactions"
      Then I should see "Transaction ID"
      And I click on the top row in Transactions
        Then I should see "Child Transactions"
      And I click on the Transaction ID link
        Then I should see "Merchant Accounting System Login"
      And I fill in "j_username" with "admin"
      And I fill in "j_password" with "noface2face"
      And I press "Login"
    Then I should see "Authorization" in the "summary"
      And I should see "VISA" in the "summary"
      And I follow "logout"
      And I move backward one page
      And I move backward one page
      And I follow "Log Out"

Feature: Vault
  In order to protect my customers credit card information
  As an admin, 
    I want to be able to see tokens in the vault table

  Background:
    Given There are no rows in the database table "vault"
    And I am using the sandbox
    
#  @javascript
#  Scenario: Tokens are stored in the vault
#    Given I am logged in as "gdake@litle.com" with the password "password"
#    When I have "vault" in my cart
#      And I press "Proceed to Checkout"
#      And I press "Continue"
#      And I press the "3rd" continue button
#      And I choose "CreditCard"
#      And I select "MasterCard" from "Credit Card Type"
#      And I fill in "Credit Card Number" with "5435101234510196"
#      And I select "11" from "Expiration Date"
#      And I select "2012" from "creditcard_expiration_yr"
#      And I fill in "Card Verification Number" with "987"
#      And I press the "4th" continue button
#      And I press "Place Order"
#    Then I should see "Thank you for your purchase"
#      And I follow "Log Out"
#      And the "vault" table should have a row with "0196" in the "last4" column
#      And the "vault" table should have a row with "1112222233330196" in the "token" column
#      And the "vault" table should have a row with "MC" in the "type" column
#      And the "vault" table should have a row with "543510" in the "bin" column
      
  @javascript
  Scenario: Non token transactions are not stored in the vault
    Given I am logged in as "gdake@litle.com" with the password "password"
    When I have "this is my product" in my cart
      And I press "Proceed to Checkout"
      And I press "Continue"
      And I press the "3rd" continue button
      And I choose "CreditCard"
      And I select "MasterCard" from "Credit Card Type"
      And I fill in "Credit Card Number" with "5435101234510196"
      And I select "11" from "Expiration Date"
      And I select "2012" from "creditcard_expiration_yr"
      And I fill in "Card Verification Number" with "987"
      And I press the "4th" continue button
      And I press "Place Order"
    Then I should see "Thank you for your purchase"
      And I follow "Log Out"
      And the "vault" should have "0" rows
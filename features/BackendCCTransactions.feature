Feature: BackendCCTransactions
  Tests to verify Backend Credit Card transactions are taking place

  Background:
    Given I am doing cc or echeck transactions
    And I am doing non paypage transactions

   @javascript @ready
  Scenario: Attempt a failed void capture
    Given I am doing Litle auth
    And I am logged in as "gdake@litle.com" with the password "password"
    When I have "affluentvisa" in my cart
      And I press "Proceed to Checkout"
      And I press "Continue"
      And I press the "3rd" continue button
      And I choose "CreditCard"
      And I select "American Express" from "Credit Card Type"
      And I put in "Credit Card Number" with "346854278192102"
      And I select "9" from "Expiration Date"
      And I select "2012" from "creditcard_expiration_yr"
      And I put in "Card Verification Number" with "1313"
      And I press the "4th" continue button
      And I press "Place Order"
    Then I should see "Thank you for your purchase"
      And I follow "Log Out"
    Given I am logged in as an administrator
    When I view "Sales" "Orders"
      Then I should see "Orders"
      And I click on the top row in Orders
        Then I should see "Order #"
        Then I should see "Litle Credit Card"
      And I press "Invoice"
      And I press "Submit Invoice"
      	Then I should see "The invoice has been created"
      And I press "Void Capture"
      	Then I should see "The void did not go through. Do a refund instead"
      And I follow "Log Out"
  
  @javascript @ready
  Scenario: Do a void capture and then capture again
    Given I am doing Litle auth
    And I am logged in as "gdake@litle.com" with the password "password"
    When I have "affluentvisa" in my cart
      And I press "Proceed to Checkout"
      And I press "Continue"
      And I press the "3rd" continue button
      And I choose "CreditCard"
      And I select "Visa" from "Credit Card Type"
      And I put in "Credit Card Number" with "4100000000000001"
      And I select "9" from "Expiration Date"
      And I select "2012" from "creditcard_expiration_yr"
      And I put in "Card Verification Number" with "123"
      And I press the "4th" continue button
      And I press "Place Order"
    Then I should see "Thank you for your purchase"
      And I follow "Log Out"
    Given I am logged in as an administrator
    When I view "Sales" "Orders"
      Then I should see "Orders"
      And I click on the top row in Orders
        Then I should see "Order #"
        Then I should see "Litle Credit Card"
      And I press "Invoice"
      And I press "Submit Invoice"
      	Then I should see "The invoice has been created"
      And I press "Void Capture"
      	Then I should see "The payment has been voided."
      And I press "Invoice"
      And I press "Submit Invoice"
      	Then I should see "The invoice has been created"
      	Then I should see "Captured amount of $50.00 online."
    And I follow "Log Out"

  @javascript @ready
  Scenario: Do a successful refund and then void the refund
    Given I am doing Litle auth
    And I am logged in as "gdake@litle.com" with the password "password"
    When I have "affluentvisa" in my cart
      And I press "Proceed to Checkout"
      And I press "Continue"
      And I press the "3rd" continue button
      And I choose "CreditCard"
      And I select "Visa" from "Credit Card Type"
      And I put in "Credit Card Number" with "4100000000000001"
      And I select "9" from "Expiration Date"
      And I select "2012" from "creditcard_expiration_yr"
      And I put in "Card Verification Number" with "123"
      And I press the "4th" continue button
      And I press "Place Order"
    Then I should see "Thank you for your purchase"
      And I follow "Log Out"
    Given I am logged in as an administrator
    When I view "Sales" "Orders"
      Then I should see "Orders"
      And I click on the top row in Orders
        Then I should see "Order #"
        Then I should see "Litle Credit Card"
      And I press "Invoice"
      And I press "Submit Invoice"
      	Then I should see "The invoice has been created"
      And I click on Invoices
      #And I press "Order Invoices"
      And I click on the top row in Invoices
      And I press "Credit Memo"
      And I click on refund
      	Then I should see "The credit memo has been created"
      And I press "Void Refund"
      	Then I should see "The payment has been voided"
    And I follow "Log Out"
 
 @javascript @ready
  Scenario: Do a successful auth checkout and then cancel the auth
    Given I am doing Litle auth
    And I am logged in as "gdake@litle.com" with the password "password"
    When I have "affluentvisa" in my cart
      And I press "Proceed to Checkout"
      And I press "Continue"
      And I press the "3rd" continue button
      And I choose "CreditCard"
      And I select "Visa" from "Credit Card Type"
      And I put in "Credit Card Number" with "4100000000000001"
      And I select "9" from "Expiration Date"
      And I select "2012" from "creditcard_expiration_yr"
      And I put in "Card Verification Number" with "123"
      And I press the "4th" continue button
      And I press "Place Order"
    Then I should see "Thank you for your purchase"
      And I follow "Log Out"
    Given I am logged in as an administrator
    When I view "Sales" "Orders"
      Then I should see "Orders"
      And I click on the top row in Orders
        Then I should see "Order #"
        Then I should see "Litle Credit Card"
      And I press "Cancel"
      Then I should see "Canceled"
    And I follow "Log Out"

  @javascript @ready
  Scenario: Do a successful auth checkout and then reverse the auth
    Given I am doing Litle auth
    And I am logged in as "gdake@litle.com" with the password "password"
    When I have "affluentvisa" in my cart
      And I press "Proceed to Checkout"
      And I press "Continue"
      And I press the "3rd" continue button
      And I choose "CreditCard"
      And I select "Visa" from "Credit Card Type"
      And I put in "Credit Card Number" with "4100000000000001"
      And I select "9" from "Expiration Date"
      And I select "2012" from "creditcard_expiration_yr"
      And I put in "Card Verification Number" with "123"
      And I press the "4th" continue button
      And I press "Place Order"
    Then I should see "Thank you for your purchase"
      And I follow "Log Out"
    Given I am logged in as an administrator
    When I view "Sales" "Orders"
      Then I should see "Orders"
      And I click on the top row in Orders
        Then I should see "Order #"
        Then I should see "Litle Credit Card"
      And I press "Auth-Reversal"
    Then I should see "The payment has been voided."
    And I follow "Log Out"

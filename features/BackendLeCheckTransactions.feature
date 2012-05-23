Feature: BackendLeCheckTransactions
  Tests to verify Backend Litle echeck transactions are taking place

  Background:
    Given I am doing Litle sale
    And I am doing non paypage transactions

@javascript @nonpaypage @ready
  Scenario: Attempt a failed void capture
    Given I am doing Litle auth
    And I am logged in as "gdake@litle.com" with the password "password"
    When I have "affluentvisa" in my cart
      And I press "Proceed to Checkout"
      And I press "Continue"
      And I press the "3rd" continue button
      And I choose "LEcheck"
      And I put in "Bank routing number" with "053100300"
      And I put in "Bank account number" with "13131313"
      And I select "Checking" from "Account type"
      And I press the "4th" continue button
      And I press "Place Order"
    Then I should see "Thank you for your purchase"
      And I follow "Log Out"
    Given I am logged in as an administrator
    When I view "Sales" "Orders"
      Then I should see "Orders"
      And I click on the top row in Orders
        Then I should see "Order #"
        Then I should see "Litle ECheck"
      And I press "Void Sale"
      	Then I should see "The void did not go through. Do a refund instead"
      And I follow "Log Out"

  @javascript @nonpaypage @ready
  Scenario: Do a void capture and then capture again
    Given I am doing Litle auth
    And I am logged in as "gdake@litle.com" with the password "password"
    When I have "affluentvisa" in my cart
      And I press "Proceed to Checkout"
      And I press "Continue"
      And I press the "3rd" continue button
      And I choose "LEcheck"
      And I put in "Bank routing number" with "123456000"
      And I put in "Bank account number" with "123456000"
      And I select "Checking" from "Account type"
      And I press the "4th" continue button
      And I press "Place Order"
    Then I should see "Thank you for your purchase"
      And I follow "Log Out"
    Given I am logged in as an administrator
    When I view "Sales" "Orders"
      Then I should see "Orders"
      And I click on the top row in Orders
        Then I should see "Order #"
        Then I should see "Litle ECheck "
      And I press "Void Sale"
      	Then I should see "The payment has been voided."
      And I press "Invoice"
      And I press "Submit Invoice"
      	Then I should see "The invoice has been created"
      	Then I should see "Captured amount of $50.00 online."
    And I follow "Log Out"

  @javascript @nonpaypage @ready
  Scenario: Do a successful auth checkout and then reverse the auth
    Given I am doing Litle auth
    And I am logged in as "gdake@litle.com" with the password "password"
    When I have "affluentvisa" in my cart
      And I press "Proceed to Checkout"
      And I press "Continue"
      And I press the "3rd" continue button
      And I choose "LEcheck"
      And I put in "Bank routing number" with "123456000"
      And I put in "Bank account number" with "123456000"
      And I select "Checking" from "Account type"
      And I press the "4th" continue button
      And I press "Place Order"
    Then I should see "Thank you for your purchase"
      And I follow "Log Out"
    Given I am logged in as an administrator
    When I view "Sales" "Orders"
      Then I should see "Orders"
      And I click on the top row in Orders
        Then I should see "Order #"
        Then I should see "Litle ECheck "
      And I click on Invoices
      #And I press "Order Invoices"
      And I click on the top row in Invoices
      And I press "Credit Memo"
      And I click on refund
      	Then I should see "The credit memo has been created"
      And I press "Void Refund"
      	Then I should see "The payment has been voided"
    And I follow "Log Out"

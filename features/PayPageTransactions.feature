Feature: PayPageTransactions
  Tests to verify transactions are taking place successfully via PayPage.

  Background:
    Given I am doing cc or echeck transactions
    And I am doing paypage transaction


  @javascript @paypage @wip
  Scenario: Do a successful checkout and then capture the auth
    Given I am doing Litle auth
    And I am logged in as "gdake@litle.com" with the password "password"
    When I have "affluentvisa" in my cart
      And I press "Proceed to Checkout"
      And I press "Continue"
      And I press the "3rd" continue button
      And I choose "CreditCard"
      And I select "Visa" from "Credit Card Type"
      And I fill in "Credit Card Number" with "4100000000000001"
      And I select "9" from "Expiration Date"
      And I select "2012" from "creditcard_expiration_yr"
      And I fill in "Card Verification Number" with "123"
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
      And I select "Capture Online" from "invoice[capture_case]"
      And I press "Submit Invoice"
    Then I should see "The invoice has been created."
    And I follow "Log Out"

 @javascript @paypage @wip
 Scenario: Backend Paypage auth checkout, then attempt to capture
    Given I am doing Litle auth
    And I am logged in as an administrator
    When I view "Sales" "Orders"
      Then I should see "Orders"
      And I press "Create New Order"
      And I click on the top row in CustomersList
      And I choose "English"
      And I press "Add Products"
      And I click on the top row in Product Table
      And I press "Add Selected Product(s) to Order"
      And I wait for the payments to appear
      And I choose "CreditCard"
    Then I should see "Litle Virtual Terminal"
      And I follow "Get shipping methods and rates"
      And I choose "Fixed Shipping"
      And I press "Submit Order"
    When I view "Sales" "Orders"
      Then I should see "Orders"
      And I click on the top row in Orders
        Then I should see "Order #"
      And I press "Invoice"
      And I select "Capture Online" from "invoice[capture_case]"
      And I press "Submit Invoice"
    Then I should see "This order was placed using Litle Virtual Terminal. Please process the capture by logging into Litle Virtual Terminal (https://vt.litle.com)."
    And I follow "Log Out"

  @javascript @paypage @wip
  Scenario: Do a successful checkout
    Given I am doing Litle sale 
    And I am logged in as "gdake@litle.com" with the password "password"
    When I have "affluentvisa" in my cart
      And I press "Proceed to Checkout"
      And I press "Continue"
      And I press the "3rd" continue button
      And I choose "CreditCard"
      And I select "Visa" from "Credit Card Type"
      And I fill in "Credit Card Number" with "4100000000000001"
      And I select "9" from "Expiration Date"
      And I select "2012" from "creditcard_expiration_yr"
      And I fill in "Card Verification Number" with "123"
      And I press the "4th" continue button
      And I press "Place Order"
    Then I should see "Thank you for your purchase"
      And I follow "Log Out"

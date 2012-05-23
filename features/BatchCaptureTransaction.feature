Feature: BatchCaptureTransaction
  Tests to verify Batch Capture transactions

  Background:
    Given I am doing cc or echeck transactions
    And I am doing non paypage transactions

  @javascript @paypage @ready
  Scenario: Do a successful batch capture of two authorized transactions and an uncesseful one with a captured transactions
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
  Given I am doing Litle sale
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
    And I select all Orders
    And I select "Capture" from "sales_order_grid_massaction-select"
    And I press "Submit"
    Then I should see "2 order(s) have been Captured"
    Then I should see "cannot be Captured"
    And I follow "Log Out"
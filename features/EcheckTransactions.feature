Feature: EcheckTransactions
  Tests to verify transactions are taking place successfully via ECheck.

Background:
  Given I am doing cc or echeck transactions

    
  @javascript @wip @echeck
  Scenario: Do a verify and then sale
  Given I am doing Litle auth
  And I am logged in as "gdake@litle.com" with the password "password"
    When I have "affluentvisa" in my cart
      And I press "Proceed to Checkout"
      And I press "Continue"
      And I press the "3rd" continue button
      And I choose "LEcheck"
      And I fill in "Bank routing number" with "123456000"
      And I fill in "Bank account number" with "123456000"
      And I select "Checking" from "Account type"
      And I press the "4th" continue button
    Then I press "Place Order"
    Then I should see "Thank you for your purchase"
      And I follow "Log Out"
    Given I am logged in as an administrator
    When I view "Sales" "Orders"
      Then I should see "Orders"
      And I click on the top row in Orders
        Then I should see "Order #"
        Then I should see "Litle ECheck"
      And I press "Invoice"
      And I select "Capture Online" from "invoice[capture_case]"
      And I press "Submit Invoice"
    Then I should see "The invoice has been created."
    And I follow "Log Out"
    
  @javascript @wip @echeck
  Scenario: Do a unsuccessful checkout
  Given I am doing Litle auth
  And I am logged in as "gdake@litle.com" with the password "password"
    When I have "echeckdecline" in my cart
      And I press "Proceed to Checkout"
      And I press "Continue"
      And I press the "3rd" continue button
      And I choose "LEcheck"
      And I fill in "Bank routing number" with "123456000"
      And I fill in "Bank account number" with "123456000"
      And I select "Checking" from "Account type"
      And I press the "4th" continue button
    Then I press "Place Order"
    Then I should not see "Thank you for your purchase"
      And I follow "Log Out"

@javascript @wip @echeck
Scenario: Backend ECheck verify, then attempt to sale
   Given I am doing Litle auth
   And I am logged in as an administrator
   When I view "Sales" "Orders"
     Then I should see "Orders"
     And I press "Create New Order"
     And I click on the top row in CustomersList
     And I choose "English"
     And I press "Add Products"
     And I click on the product "affluentvisa"
     And I press "Add Selected Product(s) to Order"
     And I wait for the payments to appear
     And I follow "Get shipping methods and rates"
     And I choose "Fixed Shipping"
     And I choose "LEcheck"
     And I fill in "Bank routing number" with "123456000"
     And I fill in "Bank account number" with "123456000"
     And I select "Checking" from "Account type"
     And I press "Submit Order"
   When I view "Sales" "Orders"
     Then I should see "Orders"
     And I click on the top row in Orders
       Then I should see "Order #"
     And I press "Invoice"
     And I select "Capture Online" from "invoice[capture_case]"
     And I press "Submit Invoice"
   Then I should see "The invoice has been created."
   And I follow "Log Out"

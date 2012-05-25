Feature: CustomerInformation
  In order to see more information about my customers
  As an admin, 
    I want to be able to go to the Litle & Co tab on a customer
    And see enhanced auth response data

  Background:
    Given There are no rows in the database table "litle_customer_insight"
    And I am using the sandbox
    
  @javascript @ready @creditcard
  Scenario: buy with visa affluent credit card
    Given I am logged in as "gdake@litle.com" with the password "password"
    When I have "affluentvisa" in my cart
      And I press "Proceed to Checkout"
      And I press "Continue"
      And I press the "3rd" continue button
      And I choose "CreditCard"
      And I select "Visa" from "Credit Card Type"
      And I put in "Credit Card Number" with "4100300018088000"
      And I select "9" from "Expiration Date"
      And I select "2012" from "creditcard_expiration_yr"
      And I put in "Card Verification Number" with "123"
      And I press the "4th" continue button
      And I press "Place Order"
    Then I should see "Thank you for your purchase"
      And I follow "Log Out"
    Given I am logged in as an administrator
    When I view "Customers" "Manage Customers"
      Then I should see "Manage Customers"
        And I click on the customer "Greg Dake" in "Manage Customers"
        Then I should see "Personal Information"
      And I follow "Litle & Co. Customer Insight"
    Then I should see "Affluent" in the column "Affluence" on the "Customer Insight" screen
    And I click on the top row in Customer Insight
      Then I should see "Order was placed using USD"
    And I follow "Log Out"

  @javascript @ready @creditcard
  Scenario: buy with visa mass affluent credit card
    Given I am logged in as "gdake@litle.com" with the password "password"
    When I have "affluentvisa" in my cart
      And I press "Proceed to Checkout"
      And I press "Continue"
      And I press the "3rd" continue button
      And I choose "CreditCard"
      And I select "Visa" from "Credit Card Type"
      And I put in "Credit Card Number" with "4100300023117000"
      And I select "11" from "Expiration Date"
      And I select "2017" from "creditcard_expiration_yr"
      And I put in "Card Verification Number" with "123"
      And I press the "4th" continue button
      And I press "Place Order"
    Then I should see "Thank you for your purchase"
      And I follow "Log Out"
    Given I am logged in as an administrator
    When I view "Customers" "Manage Customers"
      Then I should see "Manage Customers"
        And I click on the customer "Greg Dake" in "Manage Customers"
        Then I should see "Personal Information"
      And I follow "Litle & Co. Customer Insight"
    Then I should see "Mass Affluent" in the column "Affluence" on the "Customer Insight" screen
    And I follow "Log Out"

  @javascript @ready @creditcard
  Scenario: buy with prepaid card
    Given I am logged in as "gdake@litle.com" with the password "password"
    When I have "prepaidproduct" in my cart
      And I press "Proceed to Checkout"
      And I press "Continue"
      And I press the "3rd" continue button
      And I choose "CreditCard"
      And I select "Visa" from "Credit Card Type"
      And I put in "Credit Card Number" with "4100323136403000"
      And I select "8" from "Expiration Date"
      And I select "2012" from "creditcard_expiration_yr"
      And I put in "Card Verification Number" with "123"
      And I press the "4th" continue button
      And I press "Place Order"
    Then I should see "Thank you for your purchase"
      And I follow "Log Out"
    Given I am logged in as an administrator
    When I view "Customers" "Manage Customers"
      Then I should see "Manage Customers"
        And I click on the customer "Greg Dake" in "Manage Customers"
        Then I should see "Personal Information"
      And I follow "Litle & Co. Customer Insight"
    Then I should see "Gift" in the column "Prepaid Card Type" on the "Customer Insight" screen
      And I should see "Prepaid" in the column "Funding Source" on the "Customer Insight" screen
      And I should see "$15.00" in the column "Available Balance" on the "Customer Insight" screen
      And I should see "No" in the column "Reloadable" on the "Customer Insight" screen
    And I follow "Log Out"
      
  @javascript @ready @creditcard
  Scenario: buy with issuing country card
    Given I am logged in as "gdake@litle.com" with the password "password"
    When I have "affluentvisa" in my cart
      And I press "Proceed to Checkout"
      And I press "Continue"
      And I press the "3rd" continue button
      And I choose "CreditCard"
      And I select "Visa" from "Credit Card Type"
      And I put in "Credit Card Number" with "4100300002271000"
      And I select "11" from "Expiration Date"
      And I select "2012" from "creditcard_expiration_yr"
      And I put in "Card Verification Number" with "123"
      And I press the "4th" continue button
      And I press "Place Order"
    Then I should see "Thank you for your purchase"
      And I follow "Log Out"
    Given I am logged in as an administrator
    When I view "Customers" "Manage Customers"
      Then I should see "Manage Customers"
        And I click on the customer "Greg Dake" in "Manage Customers"
        Then I should see "Personal Information"
      And I follow "Litle & Co. Customer Insight"
    Then I should see "BRA" in the column "Issuing Country" on the "Customer Insight" screen
    And I follow "Log Out"

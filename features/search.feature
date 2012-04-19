Feature: Search
  In order to see a word definition
  As a website user
  I need to be able to search for a word

  @javascript
  Scenario: Buying an item with credit card
    Given I am logged in as "gdake@litle.com" with the password "password"
      And I have "This is my product" in my cart
      And I am on "/checkout/onepage/"
    When I press "Continue" at "Billing Information"
      And I press "Continue" at "Shipping Method"
      And I select "p_method_creditcard" from "payment[method]"
      And I select "Visa" from "payment[cc_type]"
      And I fill in "creditcard_cc_number" with "4100000000000001"
      And I select "1" from "creditcard_expiration"
      And I select "2017" from "creditcard_expiration_yr"
      And I fill in "creditcard_cc_cid" with "123"
      And I press "Continue" at "Payment Information"
      And I press "Place Order"
    Then I should see "Thank you for your purchase"

#  @javascript
#  Scenario: Searching for a page with autocompletion
#    Given I am on "/wiki/Main_Page"
#    When I fill in "search" with "Behavior Driv"
#    And I wait for the suggestion box to appear
#    Then I should see "Behavior Driven Development"

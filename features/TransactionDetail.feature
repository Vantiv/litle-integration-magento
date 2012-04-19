Feature: TransactionDetail
  In order to see a link to the litle payment detail screen on the magento transaction detail page
  As a user
  I need to be able to purchase something
  And As an admin
  I need to be able to see the link
  
  @javascript
  Scenario: Buying an item with credit card
    Given I am logged in as "gdake@litle.com" with the password "password"
      And I have "This is my product" in my cart
      And I press "Proceed to Checkout"
    When I press "Continue"
      And I press "button" "3"
      And I choose "p_method_creditcard" from "checkout-payment-method-load"
      And I select "Visa" from the dropbox "creditcard_cc_type"
      And I fill in "creditcard_cc_number" with "4100000000000001"
      And I select "1" from the dropbox "creditcard_expiration"
      And I select "2017" from the dropbox "creditcard_expiration_yr"
      And I fill in "creditcard_cc_cid" with "123"
      And I press "button" "4"
      And I press "Place Order"
    Then I should see "Thank you for your purchase"

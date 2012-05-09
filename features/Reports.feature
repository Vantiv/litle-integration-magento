Feature: Reports
  In order to see all of Litle's reporting data
  As an admin,
    I want to be able to go to the Reports->Litle&Co tab
    And click on links to see more information

  Background:
    Given I am using local vap
    
  @javascript @wip
  Scenario: Go to Activity Report from Magento Admin
    Given I am logged in as an administrator
    When I view "Reports" "Litle & Co" "Activity"
      Then I should see "Merchant Accounting System Login"
      And I fill in "j_username" with "admin"
      And I fill in "j_password" with "noface2face"
      And I press "Login"
    Then I should see "Activity Report" in the "summary"
      And I follow "logout"
      And I move backward one page
      And I move backward one page
      And I follow "Log Out"
      
  @javascript @wip
  Scenario: Go to Authorization Report from Magento Admin
    Given I am logged in as an administrator
    When I view "Reports" "Litle & Co" "Authorization Report"
      Then I should see "Merchant Accounting System Login"
      And I fill in "j_username" with "admin"
      And I fill in "j_password" with "noface2face"
      And I press "Login"
    Then I should see "Authorization Report" in the "summary"
      And I follow "logout"
      And I move backward one page
      And I move backward one page
      And I follow "Log Out"
      
  @javascript @wip
  Scenario: Go to BIN Lookup Report from Magento Admin
    Given I am logged in as an administrator
    When I view "Reports" "Litle & Co" "BIN Lookup"
      Then I should see "Merchant Accounting System Login"
      And I fill in "j_username" with "admin"
      And I fill in "j_password" with "noface2face"
      And I press "Login"
    Then I should see "BIN Lookup" in the "lookup"
      And I follow "logout"
      And I move backward one page
      And I move backward one page
      And I follow "Log Out"
      
  @javascript @wip
  Scenario: Go to Activity Report from Magento Admin
    Given I am logged in as an administrator
    When I view "Reports" "Litle & Co" "Activity"
      Then I should see "Merchant Accounting System Login"
      And I fill in "j_username" with "admin"
      And I fill in "j_password" with "noface2face"
      And I press "Login"
    Then I should see "Activity Report" in the "summary"
      And I follow "logout"
      And I move backward one page
      And I move backward one page
      And I follow "Log Out"
      
  @javascript @wip
  Scenario: Go to Session Report from Magento Admin
    Given I am logged in as an administrator
    When I view "Reports" "Litle & Co" "Session"
      Then I should see "Merchant Accounting System Login"
      And I fill in "j_username" with "admin"
      And I fill in "j_password" with "noface2face"
      And I press "Login"
    Then I should see "Session Report"
      And I follow "logout"
      And I move backward one page
      And I move backward one page
      And I follow "Log Out"
      
  @javascript @wip
  Scenario: Go to Settlement Report from Magento Admin
    Given I am logged in as an administrator
    When I view "Reports" "Litle & Co" "Settlement"
      Then I should see "Merchant Accounting System Login"
      And I fill in "j_username" with "admin"
      And I fill in "j_password" with "noface2face"
      And I press "Login"
    Then I should see "Settlement Report" in the "summary"
      And I follow "logout"
      And I move backward one page
      And I move backward one page
      And I follow "Log Out"
      
  @javascript @wip
  Scenario: Go to Transaction Search from Magento Admin
    Given I am logged in as an administrator
    When I view "Sales" "Litle & Co" "Transaction Search"
      Then I should see "Merchant Accounting System Login"
      And I fill in "j_username" with "admin"
      And I fill in "j_password" with "noface2face"
      And I press "Login"
    Then I should see "Transaction Search" in the "lookup"
      And I follow "logout"
      And I move backward one page
      And I move backward one page
      And I follow "Log Out"
      
  @javascript @wip
  Scenario: Go to Transaction Search from Magento Admin
    Given I am logged in as an administrator
    When I view "Sales" "Litle & Co" "Transaction Summary"
      Then I should see "Merchant Accounting System Login"
      And I fill in "j_username" with "admin"
      And I fill in "j_password" with "noface2face"
      And I press "Login"
    Then I should see "Transaction Summary Report"
      And I follow "logout"
      And I move backward one page
      And I move backward one page
      And I follow "Log Out"
    
  @javascript @wip
  Scenario: Go to Chargebacks Report from Magento Admin
    Given I am logged in as an administrator
    When I view "Sales" "Litle & Co" "Chargebacks"
      Then I should see "Merchant Accounting System Login"
      And I fill in "j_username" with "admin"
      And I fill in "j_password" with "noface2face"
      And I press "Login"
    Then I should see "Chargeback Search"
      And I follow "logout"
      And I move backward one page
      And I move backward one page
      And I follow "Log Out"
      
  @javascript @wip
  Scenario: Go to Chargebacks from Magento Admin
    Given I am logged in as an administrator
    When I view "Reports" "Litle & Co" "Chargeback Report"
      Then I should see "Merchant Accounting System Login"
      And I fill in "j_username" with "admin"
      And I fill in "j_password" with "noface2face"
      And I press "Login"
    Then I should see "Chargeback Compliance" in the "summary"
      And I follow "logout"
      And I move backward one page
      And I move backward one page
      And I follow "Log Out"
      
  @javascript @wip
  Scenario: Go to Authorization Dashboard from Magento Admin
    Given I am logged in as an administrator
    When I view "Dashboard" "Litle & Co" "Authorization Dashboard"
      Then I should see "Merchant Accounting System Login"
      And I fill in "j_username" with "admin"
      And I fill in "j_password" with "noface2face"
      And I press "Login"
    Then I should see "Authorization Dashboard"
      And I follow "logout"
      And I move backward one page
      And I move backward one page
      And I follow "Log Out"
      
  @javascript @wip
  Scenario: Go to Fraud Detection Dashboard from Magento Admin
    Given I am logged in as an administrator
    When I view "Dashboard" "Litle & Co" "Fraud Detection"
      Then I should see "Merchant Accounting System Login"
      And I fill in "j_username" with "admin"
      And I fill in "j_password" with "noface2face"
      And I press "Login"
    Then I should see "Fraud Detection"
      And I follow "logout"
      And I move backward one page
      And I move backward one page
      And I follow "Log Out"

  @javascript @wip
  Scenario: Go to Post-Deposit Fraud Impact Dashboard from Magento Admin
    Given I am logged in as an administrator
    When I view "Dashboard" "Litle & Co" "Post-Deposit Fraud Impact"
      Then I should see "Merchant Accounting System Login"
      And I fill in "j_username" with "admin"
      And I fill in "j_password" with "noface2face"
      And I press "Login"
    Then I should see "Post-Deposit Fraud Impact"
      And I follow "logout"
      And I move backward one page
      And I move backward one page
      And I follow "Log Out"
    
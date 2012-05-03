Feature: Reports
  In order to see all of Litle's reporting data
  As an admin,
    I want to be able to go to the Reports->Litle&Co tab
    And click on links to see more information

  Background:
    And I am using local vap
    
  @javascript
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
      
  @javascript
  Scenario: Go to Authorization Report from Magento Admin
    Given I am logged in as an administrator
    When I view "Reports" "Litle & Co" "Authorization"
      Then I should see "Merchant Accounting System Login"
      And I fill in "j_username" with "admin"
      And I fill in "j_password" with "noface2face"
      And I press "Login"
    Then I should see "Authorization Report" in the "summary"
      And I follow "logout"
      And I move backward one page
      And I move backward one page
      And I follow "Log Out"
      
  @javascript
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
      
  @javascript
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
      
  @javascript
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
      
  @javascript
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
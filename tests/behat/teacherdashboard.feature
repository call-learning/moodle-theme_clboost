@theme @theme_clboost @javascript
Feature: Teacher dashboard: I can edit a course I have a quick menu that leads to a full set of course edition menu in a popup
  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  Scenario: Teacher can use the context settings menu
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on ".teacherdashboard-menu [role=button]" "css_element"
    And I wait until the page is ready
    Then I should see "Teacher dashboard menu"
    And I should see "Course administration"
    And I should see "Badges"
    And I click on "Cancel" "button" in the ".modal.show .modal-footer" "css_element"
    And I log out

  Scenario: Student cannot use the context settings menu
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And ".teacherdashboard-menu [role=button]" "css_element" should not be visible
    And I log out

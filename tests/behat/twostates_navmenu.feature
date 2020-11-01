@theme_clboost @theme @tool
Feature: As I navigate through the site I can maximise the navigation menu or leave it minimized

  @javascript
  Scenario: I can maximise the menu and hide it
    Given I log in as "admin"
    And I am on homepage
    When I click on "button[aria-controls=nav-drawer]" "css_element"
    # Always wait a bit for the transition
    And I wait "2" seconds
    # Step 1. The drawer should be fully expanded
    And the "class" attribute of "#nav-drawer" "css_element" should contain "fully-expanded"
    When I click on "button[aria-controls=nav-drawer]" "css_element"
    # Always wait a bit for the transition
    And I wait "2" seconds
    # Step 2. The drawer should be hidden
    Then "#nav-drawer" "css_element" should not be visible
    When I click on "button[aria-controls=nav-drawer]" "css_element"
    # Always wait a bit for the transition
    And I wait "2" seconds
    # Step 3. The drawer should be half expanded
    And the "class" attribute of "#nav-drawer" "css_element" should not contain "fully-expanded"
    When I click on "button[aria-controls=nav-drawer]" "css_element"
    # Always wait a bit for the transition
    And I wait "2" seconds
    # Step 4. The drawer should be fully expanded
    And the "class" attribute of "#nav-drawer" "css_element" should contain "fully-expanded"

  @javascript
  Scenario: I can maximise the menu via the maximise button in the drawer
    Given I log in as "admin"
    And I am on homepage
    When I click on "#nav-drawer > .nav-drawer-maximise > ul > li > a" "css_element"
  # Always wait a bit for the transition
    And I wait "2" seconds
    And the "class" attribute of "#nav-drawer" "css_element" should contain "fully-expanded"
    Then "#nav-drawer > .nav-drawer-maximise" "css_element" should not be visible
@javascript @theme @theme_clboost
Feature: As I navigate through the site I can maximise the navigation menu or leave it minimized

  Scenario: I can maximise the menu and minimize it
    Given I log in as "admin"
    And I am on homepage
    When I click on "button[aria-controls=nav-drawer]" "css_element"
    # Always wait a bit for the transition
    And I wait "2" seconds
    # Step 1. The drawer should be fully expanded
    And the "class" attribute of "#nav-drawer" "css_element" should not contain "closed"
    When I click on "button[aria-controls=nav-drawer]" "css_element"
    # Always wait a bit for the transition
    And I wait "2" seconds
    # Step 2. The drawer should be half expanded
    And the "class" attribute of "#nav-drawer" "css_element" should contain "closed"
    When I click on "button[aria-controls=nav-drawer]" "css_element"
    # Always wait a bit for the transition
    And I wait "2" seconds
    # Step 3. The drawer should be fully expanded
    And the "class" attribute of "#nav-drawer" "css_element" should not contain "closed"

  Scenario: I can maximise the menu and hide it on small screens
    Given I log in as "admin"
    And I change window size to "360x720"
    And I am on homepage
    Then "#nav-drawer" "css_element" should not be visible
    When I click on "button[aria-controls=nav-drawer]" "css_element"
    # Always wait a bit for the transition
    And I wait "2" seconds
    # Step 1. The drawer should be fully expanded
    And the "class" attribute of "#nav-drawer" "css_element" should not contain "closed"
    And "#nav-drawer" "css_element" should be visible

  Scenario: I can maximise the menu via the maximise button in the drawer
    Given I log in as "admin"
    And I am on homepage
    When I click on "#nav-drawer > .nav-drawer-maximise-action > ul > li > a" "css_element"
  # Always wait a bit for the transition
    And I wait "2" seconds
    And the "class" attribute of "#nav-drawer" "css_element" should not contain "closed"
    Then "#nav-drawer > .nav-drawer-maximise-action" "css_element" should not be visible

  Scenario: The nav drawer is not visible on the home page when not logged in
    And I am on homepage
    Then I should not see "#nav-drawer"
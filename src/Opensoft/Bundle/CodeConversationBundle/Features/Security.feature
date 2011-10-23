Feature: Code Conversations Security
  In order to hide tools from anonymous users without a login
  As a Symfony2 developer
  I need to be able to use security component to limit access to valid users

  Scenario: User is redirected login page from homepage
    Given I am on "/"
    Then I should see "Sign in"

  Scenario Outline: User logins
    Given I am on "/"
    And I fill in "username" with "<username>"
    And fill in "password" with "<password>"
    And press "Sign in"
    Then I should see "<page content>"

    Examples:
      | username | password    | page content                        |
      | none     | wrong       | Bad credentials                     |
      | user     | wrong       | The presented password is invalid   |
      | user     | userpass    | All Projects                        |
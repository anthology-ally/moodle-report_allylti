# This file is part of Moodle - http://moodle.org/
#
# Moodle is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Moodle is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
#
# Behat feature for Ally report link.
#
# @package    report_allylti
# @copyright  Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
# @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

@report @report_allylti
Feature: Launch to Ally reports
  In order to view Ally reports
  As an Administrator
  I want to click a link to launch to Ally reports

  @javascript
  Scenario: Administrator can click a link to launch report when configured
    Given I log in as "admin"
    And I am on site homepage
    And I navigate to "Ally" node in "Site administration > Plugins > Admin tools"
    And I set the field "Launch URL" to local url "/report/allylti/tests/fixtures/report.php"
    And I set the field "Key" to "ltikey"
    And I click on "Click to enter text" "link"
    And I set the field "Secret" to "secretpassword12345"
    And I press "Save changes"
    And I navigate to "Accessibility" node in "Site administration > Reports"
    And I switch to "contentframe" iframe
    Then I should see "This represents a report launch"

  Scenario: Teacher does not see a link for the report
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Terry1    | Teacher1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    Then "Reports > Accessibility" "link" should not exist in current page administration


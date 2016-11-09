<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Settings for Ally reports.
 *
 * @package    report_allylti
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2016 Blackboard Inc. (http://www.blackboard.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings->add(new admin_setting_configtext('report_allylti/adminurl', new lang_string('adminurl', 'report_allylti'),
            new lang_string('adminurldesc', 'report_allylti'), '', PARAM_URL));

    $settings->add(new admin_setting_configtext('report_allylti/key', new lang_string('key', 'report_allylti'),
            new lang_string('keydesc', 'report_allylti'), '', PARAM_ALPHANUMEXT));

    $settings->add(new admin_setting_configpasswordunmask('report_allylti/secret',
            new lang_string('secret', 'report_allylti'), new lang_string('secretdesc', 'report_allylti'), ''));
}

$config = get_config('report_allylti');

$configured = !empty($config) && !empty($config->adminurl) && !empty($config->key) && !empty($config->secret);
if ($configured) {
    $ADMIN->add('reports', new admin_externalpage('allyadminreport', get_string('adminreport', 'report_allylti'),
            "$CFG->wwwroot/report/allylti/view.php?report=admin", 'report/allylti:viewadminreport'));
}

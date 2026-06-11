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
 * Ally report view script.
 *
 * @package    report_allylti
 * @author     Sam Chaffee
 * @copyright  Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use report_allylti\local\launch_config;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/mod/lti/locallib.php');

$undertest = defined('BEHAT_SITE_RUNNING') || PHPUNIT_TEST;
if (!$undertest && is_callable('mr_off') && mr_off('report_allylti', '_MR_MISC')) {
    new moodle_exception('notenabled', 'report_allylti');
}

$PAGE->set_context(context_system::instance());
require_login(null, false);
require_capability('report/allylti:viewadminreport', context_system::instance());
$report = required_param('report', PARAM_ALPHA);

$config = get_config('tool_ally');
$launchconfig = new launch_config($config, $report, $CFG);


$launchcontainer = $launchconfig->get_launchcontainer();

// Code from mod/lti/view.php with minor modifications.
if ($launchcontainer == LTI_LAUNCH_CONTAINER_EMBED_NO_BLOCKS) {
    $title = get_string('pluginname', 'report_allylti');
    $url = new moodle_url('/report/allylti/view.php', ['report' => $report]);
    $PAGE->set_url($url);
    $PAGE->set_title($title);
    $PAGE->set_pagelayout('frametop'); // Most frametops don't include footer, and pre-post blocks.
    $PAGE->blocks->show_only_fake_blocks(); // Disable blocks for layouts which do include pre-post blocks.
} else if ($launchcontainer == LTI_LAUNCH_CONTAINER_REPLACE_MOODLE_WINDOW) {
    redirect('launch.php?report=' . $report);
} else {
    admin_externalpage_setup('allyadminreport', '', null, '', ['pagelayout' => 'report']);
}

echo $OUTPUT->header();

// Code from mod/lti/view.php with minor modifications.
if ($launchcontainer == LTI_LAUNCH_CONTAINER_WINDOW) {
    echo "<script language=\"javascript\">//<![CDATA[\n";
    echo "window.open('launch.php?report=" . $report . "','lti');";
    echo "//]]\n";
    echo "</script>\n";
    echo "<p>" . get_string('reportnewwindow', 'report_allylti') . "</p>\n";
} else {
    // Request the launch content with an iframe tag.
    echo '<iframe id="contentframe" height="600px" width="100%" src="launch.php?report=' . $report . '"></iframe>';

    // Output script to make the iframe tag be as large as possible.
    $resize = '
        <script>
        (function() {
            var frame = document.getElementById("contentframe");
            var padding = 15;
            function resize() {
                document.body.style.overflow = "hidden";
                frame.style.height = (window.innerHeight - frame.getBoundingClientRect().top - padding) + "px";
            }
            resize();
            window.addEventListener("resize", resize);
        })();
        </script>
';

    echo $resize;
}

// Finish the page.
echo $OUTPUT->footer();

<?php

defined('MOODLE_INTERNAL') || die;

function report_allylti_extend_navigation_course($navigation, $course, $context) {
    global $PAGE, $COURSE;

    $canview = has_capability('report/allylti:viewcoursereport', context_course::instance($COURSE->id));
    if ($COURSE->id !== SITEID && $canview) {
        // For themes with flat menu, we deliberately add to the PAGE root navigation and not rely on a param passed
        // into this function.
        $url = new moodle_url('/report/allylti/launch.php', [
                'reporttype' => 'course',
                'report' => 'admin',
                'course' => $COURSE->id]
        );
        $icon = new pix_icon('i/ally_logo', '', 'report_allylti');
        $item = $PAGE->navigation->add(
            get_string('coursereport', 'report_allylti'),
            $url,
            navigation_node::TYPE_CUSTOM, null, null, $icon);
        $item->showinflatnavigation = true;

        // Non flat menu themes
        $navigation->add(get_string('coursereport', 'report_allylti'), $url, navigation_node::TYPE_SETTING, null, null, $icon);
    }


}

function report_allylti_before_standard_html_head() {
    global $PAGE;
    $PAGE->requires->js_call_amd('report_allylti/main', 'init');
}
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

namespace report_allylti\local;

use core\hook\output\before_standard_head_html_generation;

/**
 * Hook callbacks for report_allylti.
 * @package    report_allylti
 * @copyright  2024 onwards University College London {@link https://www.ucl.ac.uk/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Leon Stringer <leon.stringer@ucl.ac.uk>
 */
class hook_callbacks {
    /**
     * Callback to add JavaScript module to page.
     * @param \core\hook\output\before_standard_head_html_generation $hook
     */
    public static function before_standard_head_html_generation(before_standard_head_html_generation $hook): void {
        global $PAGE;
        $PAGE->requires->js_call_amd('report_allylti/main', 'init');
    }
}

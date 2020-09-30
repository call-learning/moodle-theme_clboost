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
 * Presets management
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_clboost\output;

defined('MOODLE_INTERNAL') || die;

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {
    use \theme_clboost\output\core_renderer_override_trait;

    /**
     * Add more info that can then be used in the mustache template.
     */
    public function get_template_additional_information() {
        global $PAGE, $CFG;

        $additionalinfo = new \stdClass();
        // To check if user is logged in , in all templates.
        $additionalinfo->isloggedin = isloggedin() && !isguestuser();
        $themename = $PAGE->theme->name;
        // To fetch the right path for an image in a theme pix folder.
        $additionalinfo->themebasepath = $CFG->dirroot . '/theme/' . $themename;
        if (isset($CFG->themedir)) {
            $additionalinfo->themebasepath = $CFG->themedir . '/' . $themename;
        }
        return $additionalinfo;
    }

}

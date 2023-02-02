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
namespace theme_clboost\output;

use stdClass;
use theme_clboost\local\utils;
/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {
    use core_renderer_override_misc;
    use core_renderer_override_logos;
    use core_renderer_override_menus;
    use core_renderer_override_mustache;

    /**
     * Add more info that can then be used in the mustache template.
     *
     * For example {{# additionalinfo.isloggedin }} {{/ additionalinfo.isloggedin }}
     *
     * @return stdClass
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_template_additional_information() {
        global $CFG;
        $additionalinfo = new stdClass();
        // To check if user is logged in , in all templates.
        $additionalinfo->isloggedin = isloggedin() && !isguestuser();
        $additionalinfo->hasnavdrawer = utils::has_nav_drawer($this->page);

        $themename = $this->page->theme->name;
        // To fetch the right path for an image in a theme pix folder.
        $additionalinfo->themebasepath = $CFG->dirroot . '/theme/' . $themename;
        if (isset($CFG->themedir)) {
            $additionalinfo->themebasepath = $CFG->themedir . '/' . $themename;
        }
        $additionalinfo->themename = $themename;
        return $additionalinfo;
    }

}


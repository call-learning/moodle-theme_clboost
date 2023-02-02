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

use coding_exception;
use moodle_exception;
use theme_config;

/**
 * Mustache template loader with Preset managements
 *
 * Get information about valid locations for mustache templates.
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mustache_template_finder extends \core\output\mustache_template_finder {
    /**
     * Helper function for getting a list of valid template directories for a specific component.
     * The main difference with core implementation is that if a template is defined in a parent theme (or a parent
     * of a parent), this will still pick it up. With the parent method, is column2 is not defined in clboost, it
     * will raise an error, although it is defined in the parent theme.
     *
     * @param string $component The component to search
     * @param string $themename The current theme name
     * @return string[] List of valid directories for templates for this compoonent. Directories are not checked for existence.
     * @throws coding_exception
     */
    public static function get_template_directories_for_component($component, $themename = '') {
        global $CFG, $PAGE;

        $dirs = parent::get_template_directories_for_component($component, $themename);

        // Default the param.
        if ($themename == '') {
            $themename = $PAGE->theme->name;
        }

        // Clean params for safety.
        $themename = clean_param($themename, PARAM_COMPONENT);

        // Find the parent themes.
        $parents = array();
        if ($themename === $PAGE->theme->name) {
            $parents = $PAGE->theme->parents;
        } else {
            $themeconfig = theme_config::load($themename);
            $parents = $themeconfig->parents;
        }
        // Now check the parent themes.
        // Search each of the parent themes second.
        foreach ($parents as $parent) {
            $dirs[] = $CFG->dirroot . '/theme/' . $parent . '/templates/';
            if (isset($CFG->themedir)) {
                $dirs[] = $CFG->themedir . '/' . $parent . '/templates/';
            }
        }

        // Normalise directories if component is not set or empty.
        return array_map(function($d) {
            return str_replace('//', '/', $d);
        }, $dirs);
    }

    /**
     * Helper function for getting a filename for a template from the template name.
     *
     * @param string $name - This is the componentname/templatename combined.
     * @param string $themename - This is the current theme name.
     * @return string
     * @throws coding_exception
     */
    public static function get_template_filepath($name, $themename = '') {

        if (strpos($name, '/') === false) {
            throw new coding_exception('Templates names must be specified as "componentname/templatename"' .
                ' (' . s($name) . ' requested) ');
        }

        list($component, $templatename) = explode('/', $name, 2);
        $component = clean_param($component, PARAM_COMPONENT);

        // If the original method had a static call, this override would not be necessary !!
        $dirs = static::get_template_directories_for_component($component, $themename);

        foreach ($dirs as $dir) {
            $candidate = $dir . $templatename . '.mustache';
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        throw new moodle_exception('filenotfound', 'error', '', null, $name);
    }
}

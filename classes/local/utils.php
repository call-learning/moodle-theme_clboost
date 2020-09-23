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
 * Theme utilities
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_clboost\local;

defined('MOODLE_INTERNAL') || die;

/**
 * Theme utilities.
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils {

    /**
     * Get either the current theme path or the clboost matching path if it does not exist
     * in the subtheme
     *
     * This is also meant to be used in the child theme
     */
    public static function get_real_theme_path($theme, $subpath = '') {
        global $CFG;
        $roodir = $CFG->themedir ? $theme->themedir : $CFG->dirroot . '/theme/';
        $themetrypath = [
            "{$roodir}/{$theme->name}/$subpath",
            "{$roodir}/clboost/$subpath",
        ];
        foreach ($themetrypath as $p) {
            if (is_dir($p) || is_file($p)) {
                return $p;
            }
        }
        throw new \moodle_exception('filenotfound',
            'theme_clboost', null, null,
            "Cannot find the right theme path {$theme->name}/{$subpath}");
    }

    /**
     * Serves any files associated with the theme settings.
     *
     * @param stdClass $course
     * @param stdClass $cm
     * @param context $context
     * @param string $filearea
     * @param array $args
     * @param bool $forcedownload
     * @param array $options
     * @return bool
     * @throws coding_exception
     */
    public static function generic_pluginfile($themename, $course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
        if ($context->contextlevel == CONTEXT_SYSTEM && ($filearea === 'logo' || $filearea === 'backgroundimage')) {
            $theme = \theme_config::load($themename);
            // By default, theme files must be cache-able by both browsers and proxies.
            if (!array_key_exists('cacheability', $options)) {
                $options['cacheability'] = 'public';
            }
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else {
            send_file_not_found();
        }
    }

}
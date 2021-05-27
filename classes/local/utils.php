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
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_clboost\local;

use coding_exception;
use context;
use context_course;
use stdClass;

defined('MOODLE_INTERNAL') || die;

/**
 * Theme utilities.
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils {

    /**
     * Get either the current theme path or the clboost matching path if it does not exist
     * in the subtheme
     *
     * This is also meant to be used in the child theme
     *
     * @param \theme_config $theme
     * @param string $subpath
     * @return string
     * @throws \moodle_exception
     */
    public static function get_real_theme_path($theme, $subpath = '') {
        global $CFG;
        $subpath = trim($subpath, '/');
        $rootdir = !empty($CFG->themedir) ? $theme->themedir : $CFG->dirroot . '/theme';
        $themetrypath = [
            "{$rootdir}/{$theme->name}/$subpath",
        ];
        foreach ($theme->parents as $parenttheme) {
            $themetrypath[] = "{$rootdir}/{$parenttheme}/{$subpath}";
        }
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
     * @param string $themename
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
    public static function generic_pluginfile($themename, $course, $cm, $context, $filearea, $args, $forcedownload,
        array $options = array()) {
        if ($context->contextlevel == CONTEXT_SYSTEM) {
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

    /**
     * Generic way to convert config for format similar to the context menu
     *
     * @param string $configtext
     * @param callable $lineparser
     * @param string $separator
     * @return array
     */
    public static function convert_from_config($configtext, $lineparser, $separator = '|') {
        $lines = explode("\n", $configtext);
        $results = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (strlen($line) == 0) {
                continue;
            }
            $settings = explode($separator, $line);
            $currentobject = new \stdClass();
            foreach ($settings as $i => $setting) {
                $setting = trim($setting);
                $lineparser($setting, $i, $currentobject);
            }
            if (!empty((array) $currentobject)) {
                $results[] = $currentobject;
            }
        }
        return $results;
    }

    /**
     * Prepare standard page
     *
     * Code is common to several layouts.
     *
     * @param \core_renderer $output
     * @param \moodle_page $page
     * @param string $blockside
     * @return array
     * @throws coding_exception
     */
    public static function prepare_standard_page($output, $page, $blockside = 'content') {
        global $CFG, $SITE;
        user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
        require_once($CFG->libdir . '/behat/lib.php');

        $hasnavdrawer = static::has_nav_drawer($page);
        if ($hasnavdrawer && isloggedin() && !isguestuser()) {
            $navdraweropen = (get_user_preferences('drawer-open-nav', 'false') == 'true');
        } else {
            $navdraweropen = false;
        }
        $extraclasses = [];
        if ($navdraweropen) {
            $extraclasses[] = 'drawer-open-left';
        }
        $bodyattributes = $output->body_attributes($extraclasses);
        $blockshtml = $output->blocks($blockside);
        $hasblocks = strpos($blockshtml, 'data-block=') !== false;
        $buildregionmainsettings = !$page->include_region_main_settings_in_header_actions();
        // If the settings menu will be included in the header then don't add it here.
        $regionmainsettingsmenu = $buildregionmainsettings ? $output->region_main_settings_menu() : false;
        $templatecontext = [
            'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
            'output' => $output,
            'hasblocks' => $hasblocks,
            'bodyattributes' => $bodyattributes,
            'navdraweropen' => $navdraweropen,
            'regionmainsettingsmenu' => $regionmainsettingsmenu,
            'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu)
        ];

        switch ($blockside) {
            case 'content':
                $templatecontext['contentblocks'] = $blockshtml;
                break;
            case 'side-pre':
                $templatecontext['sidepreblocks'] = $blockshtml;
                break;
        }
        $nav = $page->flatnav;
        $templatecontext['flatnavigation'] = $nav;
        $templatecontext['firstcollectionlabel'] = $nav->get_collectionlabel();
        return $templatecontext;
    }

    /**
     * Check if navdrawer is enabled
     *
     * Note : nav drawer is always enabled for admins.
     *
     * @param \moodle_page $page
     * @return bool|mixed|object|string|null
     * @throws \dml_exception
     */
    public static function has_nav_drawer(\moodle_page $page) {
        $currentthemename = $page->theme->name;
        $hasnavdrawer = get_config('theme_' . $currentthemename, 'hasnavdrawer');
        $result = is_siteadmin();
        $result = $result || is_null($hasnavdrawer) ? true : boolval($hasnavdrawer);
        return $result;
    }
}
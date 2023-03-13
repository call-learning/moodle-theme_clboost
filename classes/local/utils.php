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
namespace theme_clboost\local;

use coding_exception;
use context;
use context_course;
use stdClass;
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
        require_once($CFG->dirroot . '/course/lib.php');

        // Add block button in editing mode.
        $addblockbutton = $output->addblockbutton();

        user_preference_allow_ajax_update('drawer-open-index', PARAM_BOOL);
        user_preference_allow_ajax_update('drawer-open-block', PARAM_BOOL);

        if (isloggedin()) {
            $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
            $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
        } else {
            $courseindexopen = false;
            $blockdraweropen = false;
        }

        if (defined('BEHAT_SITE_RUNNING')) {
            $blockdraweropen = true;
        }

        $extraclasses = ['uses-drawers'];
        if ($courseindexopen) {
            $extraclasses[] = 'drawer-open-index';
        }

        $blockshtml = $output->blocks($blockside);
        $hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
        if (!$hasblocks) {
            $blockdraweropen = false;
        }
        $courseindex = core_course_drawer();
        if (!$courseindex) {
            $courseindexopen = false;
        }

        $bodyattributes = $output->body_attributes($extraclasses);
        $forceblockdraweropen = $output->firstview_fakeblocks();

        $secondarynavigation = false;
        $overflow = '';
        if ($page->has_secondary_navigation()) {
            $tablistnav = $page->has_tablist_secondary_navigation();
            $moremenu = new \core\navigation\output\more_menu($page->secondarynav, 'nav-tabs', true, $tablistnav);
            $secondarynavigation = $moremenu->export_for_template($output);
            $overflowdata = $page->secondarynav->get_overflow_menu_data();
            if (!is_null($overflowdata)) {
                $overflow = $overflowdata->export_for_template($output);
            }
        }

        $primary = new \core\navigation\output\primary($page);
        $renderer = $page->get_renderer('core');
        $primarymenu = $primary->export_for_template($renderer);
        $buildregionmainsettings = !$page->include_region_main_settings_in_header_actions() && !$page->has_secondary_navigation();
        // If the settings menu will be included in the header then don't add it here.
        $regionmainsettingsmenu = $buildregionmainsettings ? $output->region_main_settings_menu() : false;

        $header = $page->activityheader;
        $headercontent = $header->export_for_template($renderer);

        $templatecontext = [
            'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
            'output' => $output,
            'hasblocks' => $hasblocks,
            'bodyattributes' => $bodyattributes,
            'courseindexopen' => $courseindexopen,
            'blockdraweropen' => $blockdraweropen,
            'courseindex' => $courseindex,
            'primarymoremenu' => $primarymenu['moremenu'],
            'secondarymoremenu' => $secondarynavigation ?: false,
            'mobileprimarynav' => $primarymenu['mobileprimarynav'],
            'usermenu' => $primarymenu['user'],
            'langmenu' => $primarymenu['lang'],
            'forceblockdraweropen' => $forceblockdraweropen,
            'regionmainsettingsmenu' => $regionmainsettingsmenu,
            'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
            'overflow' => $overflow,
            'headercontent' => $headercontent,
            'addblockbutton' => $addblockbutton
        ];

        switch ($blockside) {
            case 'content':
                $templatecontext['contentblocks'] = $blockshtml;
                break;
            case 'side-pre':
                $templatecontext['sidepreblocks'] = $blockshtml;
                break;
        }

        return $templatecontext;
    }

}

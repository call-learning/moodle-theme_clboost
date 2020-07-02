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
 * Theme plugin version definition.
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Post process the CSS tree.
 *
 * @param string $tree The CSS tree.
 * @param theme_config $theme The theme config object.
 */
function theme_clboost_css_tree_post_processor($tree, $theme) {
    theme_boost_css_tree_post_processor($tree, $theme);
}

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_clboost_get_extra_scss($theme) {
    return theme_boost_get_extra_scss($theme);
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
function theme_clboost_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM && ($filearea === 'logo' || $filearea === 'backgroundimage')) {
        $theme = theme_config::load('clboost');
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
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 * @throws dml_exception
 */
function theme_clboost_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $allpresets = \theme_clboost\presets\utils::get_available_presets($theme);
    $presetname = \theme_clboost\presets\utils::get_current_preset($theme);
    $context = context_system::instance();

    if (empty($allpresets) || empty($allpresets[$presetname])) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/clboost/scss/preset/default.scss');
    } else {
        $currentpreset = $allpresets[$presetname];
        switch ($currentpreset->type) {
            case \theme_clboost\presets\utils::LOCAL_PRESET:
                $scss .= file_get_contents($currentpreset->filepath);
                break;
            case \theme_clboost\presets\utils::IMPORTED_PRESET:
                $fs = get_file_storage();
                $presetfile = $fs->get_file($context->id, 'theme_clboost', 'preset', 0, '/', $currentpreset->filepath);
                $scss .= $presetfile->get_content();
                break;
        }
    }
    // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.
    $pre = file_get_contents($CFG->dirroot . '/theme/clboost/scss/pre.scss');
    // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.
    $post = file_get_contents($CFG->dirroot . '/theme/clboost/scss/post.scss');
    return $pre. $scss . $post;
}

/**
 * Get compiled css.
 *
 * @return string compiled css
 */
function theme_clboost_get_precompiled_css() {
    global $CFG;
    return file_get_contents($CFG->dirroot . '/theme/clboost/style/moodle.css');
}

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return array
 */
function theme_clboost_get_pre_scss($theme) {
    return theme_boost_get_pre_scss($theme);
}

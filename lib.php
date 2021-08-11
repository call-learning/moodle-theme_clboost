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
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use theme_clboost\local\utils;

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
    return theme_clboost\local\utils::generic_pluginfile('clboost', $course, $cm, $context, $filearea, $args, $forcedownload,
        $options);
}

/**
 * Returns the main SCSS content.
 *
 * This includes the post.css file from CL Boost and only the pre.css file
 * from clboost if the one from the current theme is empty.
 * This will avoid the double inclusion of the font-awesome, moodle scss file.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 * @throws dml_exception
 */
function theme_clboost_get_main_scss_content($theme) {
    // Pre CSS - this is loaded AFTER any prescss from the setting but before the main scss.
    $pre = file_get_contents(utils::get_real_theme_path($theme, 'scss/pre.scss'));
    // Post CSS - this is loaded AFTER the main scss but before the extra scss from the setting.
    $post = file_get_contents(utils::get_real_theme_path($theme, 'scss/post.scss'));

    return  $pre . $theme->settings->scss . $post;
}

/**
 * Get compiled css.
 *
 * @param theme_config $theme
 * @return string compiled css
 */
function theme_clboost_get_precompiled_css($theme) {
    global $CFG;
    return file_get_contents($CFG->dirroot . '/theme/boost/style/moodle.css');
}

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 * @throws dml_exception
 */
function theme_clboost_get_pre_scss($theme) {
    $scss = '';

    $allthemeconfig = get_config('theme_' . $theme->name);

    $configurable = [];
    $prefix = 'branding_';
    foreach ($allthemeconfig as $name => $value) {
        if (substr($name, 0, strlen($prefix)) == $prefix) {
            $configurable[$name] = substr($name, strlen($prefix));
        }
    }

    // Prepend variables first.
    foreach ($configurable as $configkey => $targets) {
        $value = isset($theme->settings->{$configkey}) ? $theme->settings->{$configkey} : null;
        if (empty($value)) {
            continue;
        }
        array_map(function($target) use (&$scss, $value) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }, (array) $targets);
    }

    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    return $scss;
}

/**
 * Map icons for font-awesome themes.
 */
function theme_clboost_get_fontawesome_icon_map() {
    return [
        'theme_clboost:teacherdb' => 'fa-cogs'
    ];
}

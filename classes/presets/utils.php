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

namespace theme_clboost\presets;

defined('MOODLE_INTERNAL') || die();

class utils {

    const LOCAL_PRESET = 'local';
    const IMPORTED_PRESET = 'imported';
    /**
     * Get all available presets from the scss/preset folder
     *
     * @param null $theme
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public static function get_available_presets($theme = null) {
        global $CFG;
        // Preset stored in the source files.
        $presetrootdir = $theme ? $theme->dir . '/scss/preset' : $CFG->dirroot . '/theme/clboost/scss/preset/';
        $presets = [];
        foreach (scandir($presetrootdir) as $preset) {
            if (!in_array($preset, array(".", ".."))) {
                $presetbase = basename($preset, '.scss');
                $presetobject = (object) [
                    'type' => self::LOCAL_PRESET,
                    'filepath' => $presetrootdir . '/' . $preset,
                    'name' => get_string('presetname:'.$presetbase, 'theme_clboost')
                ];
                $presets[$presetbase] = $presetobject;
            }
        }

        // Presets stored in the data folder.
        $context = \context_system::instance();
        $fs = get_file_storage();
        $presetdatafiles = $fs->get_area_files($context->id, 'theme_clboost', 'preset', 0, 'itemid, filepath, filename', false);

        foreach ($presetdatafiles as $preset) {
            $presetbase = basename($preset, '.scss');
            $presetobject = (object) [
                'type' => self::IMPORTED_PRESET,
                'filepath' => basename($preset),
                'name' => get_string('presetname:'.$presetbase, 'theme_clboost')
            ];
            $presets[$presetbase] = $presetobject;
        }

        return $presets;
    }

    /**
     * Get current preset name (shortname)
     *
     * @param null $theme
     * @return string
     */
    public static function get_current_preset($theme = null) {
        $presetname = ($theme && $theme->settings && $theme->settings->preset) ? $theme->settings->preset : 'default';
        if (!$theme) {
            $cboostpreset = get_config('theme_clboost', 'preset');
            if ($cboostpreset) {
                $presetname = $cboostpreset;
            }
        }
        return $presetname;
    }
}
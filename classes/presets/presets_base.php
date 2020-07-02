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

abstract class presets_base {

    /**
     * Get additional setting page
     * @return \admin_settingpage
     */
    public abstract function get_additional_settings_page();

    /**
     * Get extra context for rendering
     */
    public abstract function get_extra_context();

    /**
     * Return current preset instance
     */
    public static function get_current_preset_instance() {
        $currentpresetname = get_config('theme_clboost','preset');
        $presetclassname = '\\theme_clboost\presets\\'.$currentpresetname;
        if (class_exists($presetclassname)) {
            return new $presetclassname();
        }
        return null;
    }
}
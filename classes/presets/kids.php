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

use admin_settingpage;

class kids extends presets_base {
    public function get_additional_settings_page() {
        // Advanced settings.
        $page = new admin_settingpage('theme_clboost_kids', get_string('kids:settings', 'theme_clboost'));

        // Raw SCSS to include before the content.
        $setting = new \admin_setting_configtext('theme_clboost/kids_tagline',
            get_string('kids:tagline', 'theme_clboost'),
            get_string('kids:tagline_desc', 'theme_clboost'),
            get_string('kids:slogan:default', 'theme_clboost'),
            PARAM_RAW);
        $page->add($setting);
        $setting = new \admin_setting_configtext('theme_clboost/kids_slogan',
            get_string('kids:slogan', 'theme_clboost'),
            get_string('kids:slogan_desc', 'theme_clboost'),
            get_string('kids:slogan:default', 'theme_clboost'),
            PARAM_RAW);
        $page->add($setting);
        return $page;
    }

    public function get_extra_context() {
        global $CFG;
        $clboostconfig = get_config('theme_clboost');
        return [
            'carousel' => [
                ['image' => $CFG->wwwroot . '/theme/clboost/pix/kids/carousel/img-1.jpg',
                    'label' => get_string('kids:carousel:label1', 'theme_clboost')],
                ['image' => $CFG->wwwroot . '/theme/clboost/pix/kids/carousel/img-2.jpg',
                    'label' => get_string('kids:carousel:label2', 'theme_clboost')]
            ],
            'footerimages' => [
                $CFG->wwwroot . '/theme/clboost/pix/kids/sisters.png'
            ],
            'headerimages' => [
                $CFG->wwwroot . '/theme/clboost/pix/kids/kidsplay.png',
                $CFG->wwwroot . '/theme/clboost/pix/kids/kidscards.png',
            ],
            'themeconfig' => (array) $clboostconfig
        ];
    }
}
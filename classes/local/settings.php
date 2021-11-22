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
 * All constant in one place
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_clboost\local;

use admin_setting_configcolourpicker;
use admin_setting_configstoredfile;
use admin_setting_configtext;
use admin_setting_scsscode;
use admin_settingpage;
use theme_boost_admin_settingspage_tabs;

defined('MOODLE_INTERNAL') || die;

/**
 * Theme settings. In one place.
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class settings {

    /**
     * Create settings for the current theme (clboost by default)
     *
     * @param string $currentthemename
     * @return theme_boost_admin_settingspage_tabs
     */
    public static function create_settings($currentthemename = 'clboost') {
        $themefullname = 'theme_' . $currentthemename;

        $settings =
            new theme_boost_admin_settingspage_tabs('themesetting' . $currentthemename,
                static::get_string('configtitle', $themefullname));
        $page = new admin_settingpage($themefullname . '_general', static::get_string('generalsettings', $themefullname));

        // Background image setting.
        $name = $themefullname . '/backgroundimage';
        $title = static::get_string('backgroundimage', $themefullname);
        $description = static::get_string('backgroundimage_desc', $themefullname);
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'backgroundimage');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Variable $body-color.
        // We use an empty default value because the default colour should come from the preset.
        $name = $themefullname . '/branding_primary';
        $title = static::get_string('brandcolor', $themefullname);
        $description = static::get_string('brandcolor_desc', $themefullname);
        $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Must add the page after definiting all the settings!
        $settings->add($page);

        // Advanced settings.
        $page = new admin_settingpage($themefullname . '_advanced', static::get_string('advancedsettings', $themefullname));

        $setting = new \admin_setting_configtext($themefullname . '/ganalytics',
            static::get_string('ganalytics', $themefullname),
            static::get_string('ganalytics_desc', $themefullname),
            '',
            PARAM_ALPHANUMEXT
        );
        $page->add($setting);

        $page = new admin_settingpage($themefullname . '_advanced', static::get_string('advancedsettings', $themefullname));

        $setting = new \admin_setting_configcheckbox($themefullname . '/hasnavdrawer',
            static::get_string('hasnavdrawer', $themefullname),
            static::get_string('hasnavdrawer_desc', $themefullname),
            true
        );
        $setting = new \admin_setting_configtext($themefullname . '/additionalmenusitems',
            static::get_string('additionalmenusitems', $themefullname),
            static::get_string('additionalmenusitems_desc', $themefullname),
            'participants,calendar,grades,badgesview'
        );
        $page->add($setting);
        // Raw SCSS to include before the content.
        $setting = new admin_setting_scsscode($themefullname . '/scsspre',
            static::get_string('rawscsspre', $themefullname), static::get_string('rawscsspre_desc', $themefullname), '', PARAM_RAW);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        // Raw SCSS to include after the content.
        $setting = new admin_setting_scsscode($themefullname . '/scss', static::get_string('rawscss', $themefullname),
            static::get_string('rawscss_desc', $themefullname), '', PARAM_RAW);
        $setting->set_updatedcallback('theme_reset_all_caches');
        $page->add($setting);

        $settings->add($page);

        static::additional_settings($settings);
        return $settings;
    }

    /**
     * Additional settings
     *
     * This is intended to be overriden in the subtheme to add new pages for example.
     *
     * @param admin_settingpage $settings
     */
    protected static function additional_settings(admin_settingpage &$settings) {
        // To be overriden in any sub theme.
    }

    /**
     * Make sure we fetch the string from the subtheme if it exists
     *
     * @param string $stringid
     * @param string $currentheme
     * @return string
     */
    public static function get_string($stringid, $currentheme) {
        $manager = get_string_manager();
        if ($manager->string_exists($stringid, $currentheme)) {
            return $manager->get_string($stringid, $currentheme);
        } else {
            return $manager->get_string($stringid, 'theme_clboost');
        }
    }
}

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
 * Theme plugin version settings.
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingclboost', get_string('configtitle', 'theme_clboost'));
    $page = new admin_settingpage('theme_clboost_general', get_string('generalsettings', 'theme_clboost'));

    // Preset.
    $name = 'theme_clboost/preset';
    $title = get_string('preset', 'theme_clboost');
    $description = get_string('preset_desc', 'theme_clboost');
    $default = 'default';

    $choices = array_map(
        function($p) {
            return $p->name;
        },
        \theme_clboost\presets\utils::get_available_presets());
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Preset files setting.
    $name = 'theme_clboost/presetfiles';
    $title = get_string('presetfiles','theme_clboost');
    $description = get_string('presetfiles_desc', 'theme_clboost');

    $setting = new admin_setting_configstoredfile($name, $title, $description, 'preset', 0,
        array('maxfiles' => 20, 'accepted_types' => array('.scss')));
    $page->add($setting);

    // Background image setting.
    $name = 'theme_clboost/backgroundimage';
    $title = get_string('backgroundimage', 'theme_clboost');
    $description = get_string('backgroundimage_desc', 'theme_clboost');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'backgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Variable $body-color.
    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_clboost/brandcolor';
    $title = get_string('brandcolor', 'theme_clboost');
    $description = get_string('brandcolor_desc', 'theme_clboost');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Must add the page after definiting all the settings!
    $settings->add($page);

    // Advanced settings.
    $page = new admin_settingpage('theme_clboost_advanced', get_string('advancedsettings', 'theme_clboost'));

    // Raw SCSS to include before the content.
    $setting = new admin_setting_scsscode('theme_clboost/scsspre',
        get_string('rawscsspre', 'theme_clboost'), get_string('rawscsspre_desc', 'theme_clboost'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_scsscode('theme_clboost/scss', get_string('rawscss', 'theme_clboost'),
        get_string('rawscss_desc', 'theme_clboost'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    // Specific page for the preset.
    $pinstance = \theme_clboost\presets\presets_base::get_current_preset_instance();
    if ($pinstance) {
        $settings->add($pinstance->get_additional_settings_page());
    }

}

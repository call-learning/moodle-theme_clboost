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
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    global $CFG;
    $settings = \theme_clboost\local\settings::create_settings(); // Create relevant settings.
}
if ($hassiteconfig) {
    // Add new page that will display styles as in Boostwatch. For theme designers.
    $pagedesc = get_string('boostwatchpreview', 'theme_clboost');
    $pageurl = new moodle_url($CFG->wwwroot . '/theme/clboost/tools/boostwatch.php');
    $ADMIN->add('development',
        new admin_externalpage(
            'boostwatchpreview',
            $pagedesc,
            $pageurl,
            ['moodle/site:config'] // Only for admins.
        )
    );
    $pagedesc = get_string('elementlibrary', 'theme_clboost');
    $pageurl = new moodle_url($CFG->wwwroot . '/theme/clboost/tools/elementlibrary/index.php');
    $ADMIN->add('development',
        new admin_externalpage(
            'clboostelementlibrary',
            $pagedesc,
            $pageurl,
            ['moodle/site:config'] // Only for admins.
        )
    );
}

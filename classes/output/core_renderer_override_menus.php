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
namespace theme_clboost\output;

use action_menu;
use action_menu_filler;
use action_menu_link_secondary;
use context_user;
use core_text;
use html_writer;
use moodle_url;
use navigation_node;
use pix_icon;
use stdClass;
use theme_clboost\local\utils;

/**
 * Core renderer functionalities
 *
 * This trait is hopefully temporary. Here we override functions from core renderer
 * that would need to be broken down into more manageable (and configurable pieces)
 *
 * @package theme_clboost
 */
trait core_renderer_override_menus {
    /**
     * Override usermenu, remove 'Your are not logged in' message
     * @param stdClass $user A user object, usually $USER.
     * @param bool $withlinks true if a dropdown should be built.
     * @return string HTML fragment.
     */
    public function user_menu($user = null, $withlinks = null) {
        global $USER;
        // Get some navigation opts.
        if (is_null($user)) {
            $user = $USER;
        }
        $opts = user_get_user_navigation_info($user, $this->page);

        $loginpage = $this->is_login_page();
        $loginurl = get_login_url();
        if (!empty($opts->unauthenticateduser)) {
            $returnstr = '';
            // If not logged in, show the typical not-logged-in string.
            if (!$loginpage && (!$opts->unauthenticateduser['guest'] || $withlinks)) {
                return html_writer::link(new moodle_url($loginurl), get_string('login'), ['class' => 'nav-link']);
            }
            return '';
        }
        return parent::user_menu($user, $withlinks);
    }
}

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
     * Course additional menu
     * Show all course menu option in one go
     */
    public function page_heading_button() {
        $currentheader = parent::page_heading_button();
        if ($this->page->course && $this->page->course->id != SITEID) {
            $settingsnode = $this->page->settingsnav->find('courseadmin', navigation_node::TYPE_COURSE);
            if ($settingsnode) {
                $currentheader = $this->render(new teacherdashboard_menu($this->page->course)) . $currentheader;
            }
        }
        return $currentheader;
    }
}

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
 * Core renderer functionalities
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_clboost\output;

use action_menu;
use action_menu_filler;
use action_menu_link_secondary;
use core_text;
use html_writer;
use moodle_url;
use pix_icon;
use stdClass;
use theme_clboost\local\utils;

defined('MOODLE_INTERNAL') || die;

/**
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
            if ($this->page->user_allowed_editing()) {
                $currentheader = $this->render(new teacherdashboard_menu($this->page->course)) . $currentheader;
            }
        }
        return $currentheader;
    }

    /**
     * This is a integral copy of the core_renderer method as we will need to override
     * the trigger button class and remove bracket around the connexion link.
     *
     * In case we are not showing the side menu, some additional items will need to be added (like
     * preferences, grades, ...)
     *
     * @param stdClass $user A user object, usually $USER.
     * @param bool $withlinks true if a dropdown should be built.
     * @return string HTML fragment.
     * @throws \coding_exception
     */
    public function user_menu($user = null, $withlinks = null) {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/user/lib.php');

        if (is_null($user)) {
            $user = $USER;
        }

        // Note: this behaviour is intended to match that of core_renderer::login_info,
        // but should not be considered to be good practice; layout options are
        // intended to be theme-specific. Please don't copy this snippet anywhere else.
        if (is_null($withlinks)) {
            $withlinks = empty($this->page->layout_options['nologinlinks']);
        }

        // Add a class for when $withlinks is false.
        $usermenuclasses = 'usermenu nav-link';
        if (!$withlinks) {
            $usermenuclasses .= ' withoutlinks';
        }

        $returnstr = "";

        // If during initial install, return the empty return string.
        if (during_initial_install()) {
            return $returnstr;
        }

        $loginpage = $this->is_login_page();
        $loginurl = get_login_url();
        // If not logged in, show the typical not-logged-in string.
        if (!isloggedin()) {
            $returnstr = ''; // CL BOOST : We change this here.
            if (!$loginpage && $withlinks) {
                $returnstr = "<a href=\"$loginurl\">" . get_string('login') . '</a>';
                // CL BOOST : We change this here.
            }
            return html_writer::div(
                html_writer::span(
                    $returnstr,
                    'login'
                ),
                $usermenuclasses
            );

        }

        // If logged in as a guest user, show a string to that effect.
        if (isguestuser()) {
            $returnstr = ''; // CL BOOST : We change this here.
            if (!$loginpage && $withlinks) {
                $returnstr = "<a href=\"$loginurl\">" . get_string('login') . '</a>';
                // CL BOOST : We change this here.
            }

            return html_writer::div(
                html_writer::span(
                    $returnstr,
                    'login'
                ),
                $usermenuclasses
            );
        }

        // Get some navigation opts.
        $opts = user_get_user_navigation_info($user, $this->page);

        // CLBOOST Specific.

        if (!utils::has_nav_drawer($this->page)) {
            if ($this->page->context->contextlevel == CONTEXT_COURSE
                || $this->page->context->contextlevel == CONTEXT_MODULE) {
                // Here we add additional menus.
                // Links: Dashboard.
                $course = $this->page->course;
                $dashboardmenu = array_shift($opts->navitems);
                $this->additional_user_menus_nonavbar($opts, $course);
                if ($this->page->user_can_edit_blocks() && $this->page->user_is_editing()) {
                    $url = new moodle_url($this->page->url, ['bui_addblock' => '', 'sesskey' => sesskey()]);
                    array_unshift($opts->navitems,
                        (object) [
                            'itemtype' => 'link',
                            'url' => $url,
                            'title' => get_string('addblock'),
                            'titleidentifier' => 'addblock',
                            'pix' => 'i/addblock'
                        ],
                        (object) [
                            'itemtype' => 'divider']);
                    $addable = $this->page->blocks->get_addable_blocks();
                    $blocks = [];
                    foreach ($addable as $block) {
                        $blocks[] = $block->name;
                    }
                    $params = array('blocks' => $blocks, 'url' => '?' . $url->get_query_string(false));
                    $this->page->requires->js_call_amd('core/addblockmodal', 'init', array($params));
                }
                if (!empty($course)) {
                    array_unshift($opts->navitems,
                        (object) [
                            'itemtype' => 'link',
                            'url' => new \moodle_url('/calendar/view.php', ['view' => 'month', 'course' => $course->id]),
                            'title' => get_string('courseevent', 'calendar'),
                            'titleidentifier' => 'coursecalendar',
                            'pix' => 'i/calendar'
                        ],
                        (object) [
                            'itemtype' => 'link',
                            'url' => new \moodle_url('/grade/report/grader/index.php', ['id' => $course->id]),
                            'title' => get_string('coursegrades'),
                            'titleidentifier' => 'coursegrades',
                            'pix' => 'i/grades'
                        ], (object) [
                            'itemtype' => 'link',
                            'url' => new \moodle_url('/badges/view.php', ['type' => BADGE_TYPE_COURSE, 'id' => $course->id]),
                            'title' => get_string('coursebadges', 'badges'),
                            'titleidentifier' => 'coursebadges',
                            'pix' => 'i/badge'
                        ],
                        (object) [
                            'itemtype' => 'divider'],
                    );
                }
                array_unshift($opts->navitems, $dashboardmenu);
            }
        }
        // END CLBOOST Specific.
        $avatarclasses = "avatars";
        $avatarcontents = html_writer::span($opts->metadata['useravatar'], 'avatar current');
        $usertextcontents = $opts->metadata['userfullname'];

        // Other user.
        if (!empty($opts->metadata['asotheruser'])) {
            $avatarcontents .= html_writer::span(
                $opts->metadata['realuseravatar'],
                'avatar realuser'
            );
            $usertextcontents = $opts->metadata['realuserfullname'];
            $usertextcontents .= html_writer::tag(
                'span',
                get_string(
                    'loggedinas',
                    'moodle',
                    html_writer::span(
                        $opts->metadata['userfullname'],
                        'value'
                    )
                ),
                array('class' => 'meta viewingas')
            );
        }

        // Role.
        if (!empty($opts->metadata['asotherrole'])) {
            $role = core_text::strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['rolename'])));
            $usertextcontents .= html_writer::span(
                $opts->metadata['rolename'],
                'meta role role-' . $role
            );
        }

        // User login failures.
        if (!empty($opts->metadata['userloginfail'])) {
            $usertextcontents .= html_writer::span(
                $opts->metadata['userloginfail'],
                'meta loginfailures'
            );
        }

        // MNet.
        if (!empty($opts->metadata['asmnetuser'])) {
            $mnet = strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['mnetidprovidername'])));
            $usertextcontents .= html_writer::span(
                $opts->metadata['mnetidprovidername'],
                'meta mnet mnet-' . $mnet
            );
        }

        $returnstr .= html_writer::span(
            html_writer::span($usertextcontents, 'usertext mr-1') .
            html_writer::span($avatarcontents, $avatarclasses),
            'userbutton'
        );

        // Create a divider (well, a filler).
        $divider = new action_menu_filler();
        $divider->primary = false;

        $am = new action_menu();
        $am->set_menu_trigger(
            $returnstr,
            'nav-link' // CL BOOST : We changed this here.
        );
        $am->set_action_label(get_string('usermenu'));
        $am->set_alignment(action_menu::TR, action_menu::BR);
        $am->set_nowrap_on_items();
        if ($withlinks) {
            $navitemcount = count($opts->navitems);
            $idx = 0;
            foreach ($opts->navitems as $key => $value) {

                switch ($value->itemtype) {
                    case 'divider':
                        // If the nav item is a divider, add one and skip link processing.
                        $am->add($divider);
                        break;

                    case 'invalid':
                        // Silently skip invalid entries (should we post a notification?).
                        break;

                    case 'link':
                        // Process this as a link item.
                        $pix = null;
                        if (isset($value->pix) && !empty($value->pix)) {
                            $pix = new pix_icon($value->pix, '', null, array('class' => 'iconsmall'));
                        } else if (isset($value->imgsrc) && !empty($value->imgsrc)) {
                            $value->title = html_writer::img(
                                    $value->imgsrc,
                                    $value->title,
                                    array('class' => 'iconsmall')
                                ) . $value->title;
                        }

                        $al = new action_menu_link_secondary(
                            $value->url,
                            $pix,
                            $value->title,
                            array('class' => 'icon')
                        );
                        if (!empty($value->titleidentifier)) {
                            $al->attributes['data-title'] = $value->titleidentifier;
                        }
                        $am->add($al);
                        break;
                }

                $idx++;

                // Add dividers after the first item and before the last item.
                if ($idx == 1 || $idx == $navitemcount - 1) {
                    $am->add($divider);
                }
            }
        }

        return html_writer::div(
            $this->render($am),
            $usermenuclasses
        );
    }

    /**
     * Allow for additional user menu in navigation bar in case we have no boost navbar.
     *
     * @param object $opts $returnobj navigation information object (see @user_get_user_navigation_info)
     * @param object $course
     */
    protected function additional_user_menus_nonavbar(&$opts, $course) {
        // Add $opts->navitems[] here.
        // Nothing for now.
    }
}
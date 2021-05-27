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
 * Teacher menu modal
 *
 * @package   theme_clboost
 * @copyright 2021 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_clboost\output;

use action_link;
use coding_exception;
use context_course;
use moodle_exception;
use moodle_page;
use moodle_url;
use navigation_node;
use pix_icon;
use renderable;
use renderer_base;
use settings_navigation;
use stdClass;
use templatable;

/**
 * Class teacherdashboard_menu
 *
 * @package   theme_clboost
 * @copyright 2021 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class teacherdashboard_menu implements renderable, templatable {

    /**
     * @var object $course current course
     */
    protected $course = null;

    /**
     * teacherdashboard_menu constructor.
     *
     * @param object $course
     */
    public function __construct($course) {
        $this->course = $course;
    }

    /**
     * Export for template
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $page = new moodle_page();
        $page->set_url('/course/admin.php', array('courseid' => $this->course->id));
        $page->set_course($this->course);
        $page->set_context(context_course::instance($this->course->id));
        $page->set_pagelayout('incourse');

        new settings_navigation($page);

        $courseadminnode = $page->settingsnav->find('courseadmin', navigation_node::TYPE_COURSE);
        if ($courseadminnode) {
            $data->nodejson = json_encode(['node' => $this->get_simple_node($courseadminnode)]);
            $action = new action_link(
                new moodle_url(''),
                '',
                null,
                array('class' => 'btn btn-outline-dark', 'role' => 'button'),
                new pix_icon('teacherdb', get_string('teacherdashboardmenu', 'theme_clboost'), 'theme_clboost')
            );
            $data->action = $action->export_for_template($output);
        }
        return $data;
    }

    /**
     * Get simple node
     *
     * @param navigation_node $originnode
     * @return stdClass
     * @throws moodle_exception
     */
    protected function get_simple_node(navigation_node $originnode) {
        $node = new stdClass();
        $node->text = $originnode->text;
        $node->key = $originnode->key;
        $node->action = (new moodle_url($originnode->action))->out();
        $node->display = $originnode->display;
        $node->is_short_branch = $originnode->is_short_branch();
        $node->children = [];
        foreach ($originnode->children as $child) {
            $node->children[] = $this->get_simple_node($child);
        }
        // In the original core template we use "children.count" which does not
        // work in javascript, so we had to find a way to check if the list was empty or not
        // This is the same workaround as in Totara: https://help.totaralearning.com/display/DEV/Mustache+templates .
        $node->has_children = count($node->children) > 0;
        return $node;
    }
}
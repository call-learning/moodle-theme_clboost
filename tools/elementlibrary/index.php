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
 * An Element Library to help theme development.
 *
 * This code has been taken and slightly adapted from :
 * https://github.com/totara/moodle/tree/mdl-feature-element-library
 * and https://tracker.moodle.org/browse/MDL-36558 but never landed in moodle core.
 * This is still work in progress.  The original copyright seems to be from
 * Simon Coggins, but has been authored by various developpers from Totara.
 *
 * @package   theme_clboost
 * @copyright Simon Coggins
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '../../../../../config.php');
global $CFG, $PAGE, $OUTPUT;
require_once($CFG->libdir . '/adminlib.php');
$strheading = 'Element Library';
$url = new moodle_url('/theme/clboost/tools/elementlibrary/index.php');

// Start setting up the page.
$params = array();
$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

admin_externalpage_setup('clboostelementlibrary');

echo $OUTPUT->header();

echo $OUTPUT->heading($strheading);

echo $OUTPUT->box_start();
echo $OUTPUT->container('This page contains a set of sample elements used on this site. '.
    'It can be used to ensure that everything has been correctly themed (remember to check in a right-to-left language too), '.
    'and for developers to see examples of how to implement particular elements. Developers: if you need an element that is not '.'
    represented here, add it here first - the idea is to build up a library of all the elements used across the site.');

echo $OUTPUT->container_start();
echo $OUTPUT->heading('Moodle elements', 3);
echo html_writer::start_tag('ul');
echo html_writer::tag('li', html_writer::link(new moodle_url('headings.php'), 'Headings'));
echo html_writer::tag('li', html_writer::link(new moodle_url('common.php'), 'Common tags'));
echo html_writer::tag('li', html_writer::link(new moodle_url('lists.php'), 'Lists'));
echo html_writer::tag('li', html_writer::link(new moodle_url('tables.php'), 'Tables'));
echo html_writer::tag('li', html_writer::link(new moodle_url('forms.php'), 'Form elements'));
echo html_writer::tag('li', html_writer::link(new moodle_url('mform.php'), 'Moodle form elements'));
echo html_writer::tag('li', html_writer::link(new moodle_url('tabs.php'), 'Moodle tab bar elements'));
echo html_writer::tag('li', html_writer::link(new moodle_url('paging.php'), 'Paging bar'));
echo html_writer::tag('li', html_writer::link(new moodle_url('images.php'), 'Images'));
echo html_writer::tag('li', html_writer::link(new moodle_url('notifications.php'), 'Notifications'));
echo html_writer::tag('li', html_writer::link(new moodle_url('pagelayouts.php'), 'Page Layouts'));
echo html_writer::end_tag('ul');
echo $OUTPUT->container_end();

echo $OUTPUT->box_end();

echo $OUTPUT->footer();

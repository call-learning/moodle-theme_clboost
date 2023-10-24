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
require_once('lib.php');

$strheading = 'Element Library: Headings';
$url = new moodle_url('/theme/clboost/tools/elementlibrary/headings.php');

// Start setting up the page.
$params = [];
$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

admin_externalpage_setup('clboostelementlibrary');
echo $OUTPUT->header();

echo html_writer::link(new moodle_url('index.php'), '&laquo; Back to index');
echo $OUTPUT->heading($strheading);

echo $OUTPUT->box_start();
echo $OUTPUT->container('Examples of different types of headings:');

echo $OUTPUT->container_start();
echo $OUTPUT->heading('H1 Heading', 1);
echo $OUTPUT->heading('H2 Heading', 2);
echo $OUTPUT->heading('H3 Heading', 3);
echo $OUTPUT->heading('H4 Heading', 4);
echo $OUTPUT->heading('H5 Heading', 5);
echo $OUTPUT->heading('H6 Heading', 6);

echo $OUTPUT->container('Examples showing default spacing before and after the headings:');

echo html_writer::empty_tag('hr');
echo html_writer::tag('p', LOREMIPSUM);
echo $OUTPUT->heading('H1 Heading', 1);
echo html_writer::tag('p', LOREMIPSUM);

echo html_writer::empty_tag('hr');
echo html_writer::tag('p', LOREMIPSUM);
echo $OUTPUT->heading('H2 Heading', 2);
echo html_writer::tag('p', LOREMIPSUM);

echo html_writer::empty_tag('hr');
echo html_writer::tag('p', LOREMIPSUM);
echo $OUTPUT->heading('H3 Heading', 3);
echo html_writer::tag('p', LOREMIPSUM);

echo html_writer::empty_tag('hr');
echo html_writer::tag('p', LOREMIPSUM);
echo $OUTPUT->heading('H4 Heading', 4);
echo html_writer::tag('p', LOREMIPSUM);

echo html_writer::empty_tag('hr');
echo html_writer::tag('p', LOREMIPSUM);
echo $OUTPUT->heading('H5 Heading', 5);
echo html_writer::tag('p', LOREMIPSUM);

echo html_writer::empty_tag('hr');
echo html_writer::tag('p', LOREMIPSUM);
echo $OUTPUT->heading('H6 Heading', 6);
echo html_writer::tag('p', LOREMIPSUM);

echo html_writer::empty_tag('hr');
echo $OUTPUT->container(LOREMIPSUM);
echo $OUTPUT->heading('H1 Heading', 1);
echo $OUTPUT->container(LOREMIPSUM);

echo html_writer::empty_tag('hr');
echo $OUTPUT->container(LOREMIPSUM);
echo $OUTPUT->heading('H2 Heading', 2);
echo $OUTPUT->container(LOREMIPSUM);

echo html_writer::empty_tag('hr');
echo $OUTPUT->container(LOREMIPSUM);
echo $OUTPUT->heading('H3 Heading', 3);
echo $OUTPUT->container(LOREMIPSUM);

echo html_writer::empty_tag('hr');
echo $OUTPUT->container(LOREMIPSUM);
echo $OUTPUT->heading('H4 Heading', 4);
echo $OUTPUT->container(LOREMIPSUM);

echo html_writer::empty_tag('hr');
echo $OUTPUT->container(LOREMIPSUM);
echo $OUTPUT->heading('H5 Heading', 5);
echo $OUTPUT->container(LOREMIPSUM);

echo html_writer::empty_tag('hr');
echo $OUTPUT->container(LOREMIPSUM);
echo $OUTPUT->heading('H6 Heading', 6);
echo $OUTPUT->container(LOREMIPSUM);

echo $OUTPUT->container_end();

echo $OUTPUT->box_end();

echo $OUTPUT->footer();

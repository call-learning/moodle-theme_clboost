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

$strheading = 'Element Library: Paging';
$url = new moodle_url('/theme/clboost/tools/elementlibrary/paging.php');

// Start setting up the page.
$params = array();
$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

admin_externalpage_setup('clboostelementlibrary');

echo $OUTPUT->header();

echo html_writer::link(new moodle_url('index.php'), '&laquo; Back to index');

echo $OUTPUT->heading($strheading);

echo $OUTPUT->box_start();

echo $OUTPUT->container_start();

echo "<p>Pages 1-7 of 7</p>";
$pages = 7;
$perpage = 100;
$totalcount = $perpage * $pages;
$baseurl = '?';
$pagevar = 'page';
for ($i = 0; $i < $pages; $i++) {
    echo $OUTPUT->paging_bar($totalcount, $i, $perpage, $baseurl, $pagevar);
}

echo "<p>Pages 1-20 of 20</p>";
$pages = 20;
$totalcount = $perpage * $pages;
for ($i = 0; $i < $pages; $i++) {
    echo $OUTPUT->paging_bar($totalcount, $i, $perpage, $baseurl, $pagevar);
}

echo "<p>Pages 50-60 of 60</p>";
$pages = 60;
$totalcount = $perpage * $pages;
for ($i = 49; $i < $pages; $i++) {
    echo $OUTPUT->paging_bar($totalcount, $i, $perpage, $baseurl, $pagevar);
}
echo $OUTPUT->container_end();

echo $OUTPUT->box_end();

echo $OUTPUT->footer();

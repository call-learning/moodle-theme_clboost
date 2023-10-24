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

$strheading = 'Element Library: Example page';
$url = new moodle_url('/theme/clboost/tools/elementlibrary/example.php');

// Start setting up the page.
$params = [];
$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

$PAGE->requires->css('/theme/clboost/tools/elementlibrary/google-code-prettify/prettify.css');
$PAGE->requires->js('/theme/clboost/tools/elementlibrary/google-code-prettify/prettify.js');
$PAGE->requires->js_init_call('M.tool_elementlibrary.prettyprint');

admin_externalpage_setup('clboostelementlibrary');
echo $OUTPUT->header();

echo html_writer::link(new moodle_url('index.php'), '&laquo; Back to index');
echo $OUTPUT->heading($strheading);

echo $OUTPUT->box('This is an example of how we could use a single <a href="example.txt">source file</a> to '.
    'display the code for the developer, an example of the output, and the html source.');

echo $OUTPUT->container('<p>This is the code you should use to create a top level heading:</p>');

echo '<pre class="prettyprint linenums lang-php">';
echo get_html_output('example.txt');
echo '</pre>';

echo $OUTPUT->container('<p>Which will look like this:</p>');

echo '<div class="docs-example">';
require('example.txt');
echo '</div>';

echo $OUTPUT->container('<p>and here is the HTML output that is generated:</p>');

echo '<pre class="prettyprint linenums lang-html">';
echo get_source_output('example.txt');
echo '</pre>';

echo $OUTPUT->footer();

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
 * Boostwatch preview
 *
 * Show a preview of all elements in the current theme settings. This will help
 * to check if all is defined correctly in the scss override.
 * This is while we wait for a finished element library implementation (
 * https://tracker.moodle.org/browse/MDL-45826)
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

global $CFG, $PAGE, $OUTPUT;
require_once($CFG->libdir . '/adminlib.php');
require_login();
require_capability('moodle/site:config', context_system::instance());
admin_externalpage_setup('boostwatchpreview');

// Override pagetype to show blocks properly.
$header = get_string('boostwatchpreview', 'theme_clboost');
$PAGE->set_title($header);
$PAGE->set_heading($header);
$pageurl = new moodle_url($CFG->wwwroot . '/theme/clboost/tools/boostwatch.php');
$PAGE->set_url($pageurl);

$boostwatchurl = $CFG->wwwroot . '/theme/clboost/tools/boostwatch-content.php';
echo $OUTPUT->header();
echo $OUTPUT->action_icon(
    $boostwatchurl,
    new \pix_icon('e/search', get_string('view')),
    new \popup_action('click', $boostwatchurl)
);
echo html_writer::tag('iframe', '',
    array('id' => 'main',
        'name' => 'main',
        'width' => '100%',
        'height' => '800px',
        'src' => $boostwatchurl));

echo $OUTPUT->footer();

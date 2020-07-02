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
 * Theme plugin version definition.
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

defined('MOODLE_INTERNAL') || die();

$bodyattributes = $OUTPUT->body_attributes([]);
$contextcoursesite = context_course::instance(SITEID);


// Here we render the templates that are related to the preset, if not we display the default information.
$presetcontent = '';
try {
    $clboostconfig = get_config('theme_clboost');
    $presetname = \theme_clboost\presets\utils::get_current_preset();
    // Specific context for the preset.
    $pinstance = \theme_clboost\presets\presets_base::get_current_preset_instance();
    $currentcontext = [
        'loggedin' => isloggedin(),
        'themeconfig' => (array) $clboostconfig
    ];
    if ($pinstance) {
        $currentcontext = array_merge($currentcontext, $pinstance->get_extra_context());
    }
    $presetcontent  = $OUTPUT->render_from_template("theme_clboost/{$presetname}-frontpage-content", $currentcontext);
} catch (moodle_exception $e) {
    // We just carry on if the template is not found.
}

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'canedit' => has_capability('moodle/course:update', $contextcoursesite),
    'bodyattributes' => $bodyattributes,
    'presetcontent' => $presetcontent
];
/* @var core_renderer $OUTPUT */
echo $OUTPUT->render_from_template('theme_clboost/frontpage', $templatecontext);



// Bit of a hack here: we prevent the index page from displaying anything else than we decided to in the template.
// It would usually display the course list, news, and so on (see @core_renderer::frontpage).
$CFG->frontpage = '';
$CFG->frontpageloggedin = '';


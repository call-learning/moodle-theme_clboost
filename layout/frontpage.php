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
 * This defines a frontpage that will allow adding block to the main content area.
 *
 * @package   theme_clboost
 * @copyright 2020 - CALL Learning - Laurent David <laurent@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$templatecontext = \theme_clboost\local\utils::prepare_standard_page($OUTPUT, $PAGE);
// Bit of a hack here: we prevent the index page from displaying anything else than we decided to in the template.
// It would usually display the course list, news, and so on (see @core_renderer::frontpage).
$CFG->frontpage = '';
$CFG->frontpageloggedin = '';

echo $OUTPUT->render_from_template('theme_clboost/frontpage', $templatecontext);



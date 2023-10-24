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

$strheading = 'Element Library: Tables';
$url = new moodle_url('/theme/clboost/tools/elementlibrary/tables.php');

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
echo $OUTPUT->container('Examples of different types of tables.');

echo $OUTPUT->container_start();

echo $OUTPUT->heading('Standard moodle tables (class "generaltable")', 3);

echo $OUTPUT->heading('Simplest way to make a table', 4);

$table = new html_table();
$table->head = ['Name', 'Grade'];
$table->data = [
    ['Harry Potter', '76%'],
    ['Hermione Granger', '100%'],
];
echo html_writer::table($table);

echo $OUTPUT->heading('Column headings spanning multiple columns', 4);

// With column heading spanning multiple columns.
$table = new html_table();
$table->head = ['Name', 'Location'];
$table->headspan = [1, 2];
$table->data = [
    ['Bob', '75S', '24E'],
    ['Joe', '10N', '145W'],
];
echo html_writer::table($table);

echo $OUTPUT->heading('With fine-grained control over cells', 4);

$table = new html_table();
$cell1 = new html_table_cell();
$cell1->text = 'This cell has a colspan applied';
$cell1->colspan = 2;
$row1 = new html_table_row();
$row1->cells[] = $cell1;
$cell2 = new html_table_cell();
$cell2->text = 'Normal cell';
$cell3 = new html_table_cell();
$cell3->text = 'Normal cell';
$row2 = new html_table_row();
$row2->cells = [$cell2, $cell3];
$table->data = [$row1, $row2];
echo html_writer::table($table);

echo $OUTPUT->heading('Applying classes and ids to every element in a table', 4);

// Don't use align, size or wrap properties - assign all styles via stylesheets using classes instead.
$table = new html_table();
$table->head = ['Name', 'Grade'];
$table->rowclasses = ['first-row-class', 'second-row-class'];
$table->colclasses = ['first-col-class', 'second-col-class'];
$table->data = [
    ['Joe Bloggs', '76%'],
    ['Jane Doe', '100%'],
];
echo html_writer::table($table);

echo $OUTPUT->heading('Applying more specific ids/classes to any element', 4);

// Applying more specific ids/classes to any element.
$table = new html_table();
$head1 = new html_table_cell();
$head1->text = 'Left Head';
$head1->header = true;
$head1->id = 'left-head-id';
$head1->attributes['class'] = 'left-head-class';
$head2 = new html_table_cell();
$head2->text = 'Right Head';
$head2->header = true;
$head2->id = 'right-head-id';
$head2->attributes['class'] = 'right-head-class';
$head = new html_table_row();
$head->cells = [$head1, $head2];
$head->id = 'header-id';
$head->attributes['class'] = 'header-class';
$cell1 = new html_table_cell();
$cell1->id = 'top-left-cell-id';
$cell1->attributes['class'] = 'top-left-cell-class';
$cell1->text = 'Top Left';
$cell2 = new html_table_cell();
$cell2->id = 'top-right-cell-id';
$cell2->attributes['class'] = 'top-right-cell-class';
$cell2->text = 'Top Right';
$row1 = new html_table_row();
$row1->cells = [$cell1, $cell2];
$row1->id = 'row1-id';
$row1->attributes['class'] = 'row1-class';
$cell3 = new html_table_cell();
$cell3->id = 'bottom-left-cell-id';
$cell3->attributes['class'] = 'bottom-left-cell-class';
$cell3->text = 'Bottom Left';
$cell4 = new html_table_cell();
$cell4->id = 'bottom-right-cell-id';
$cell4->attributes['class'] = 'bottom-right-cell-class';
$cell4->text = 'Bottom Right';
$row2 = new html_table_row();
$row2->cells = [$cell3, $cell4];
$row2->id = 'row2-id';
$row2->attributes['class'] = 'row2-class';
$table->data = [$head, $row1, $row2];
echo html_writer::table($table);


echo $OUTPUT->heading('Example of a table with headings in row instead of column', 4);
// Note the use of 'scope' for accessibility. See:
// http://www.w3.org/TR/WCAG20-TECHS/H63.
$table = new html_table();
$head1 = new html_table_cell();
$head1->text = 'First Heading';
$head1->scope = 'row';
$head1->header = true;
$cell1 = new html_table_cell();
$cell1->text = 'Data associated with First Heading';
$cell2 = new html_table_cell();
$cell2->text = 'More data associated with First Heading';
$row1 = new html_table_row();
$row1->cells = [$head1, $cell1, $cell2];
$head2 = new html_table_cell();
$head2->text = 'Second Heading';
$head2->scope = 'row';
$head2->header = true;
$cell3 = new html_table_cell();
$cell3->text = 'Data associated with Second Heading';
$cell4 = new html_table_cell();
$cell4->text = 'More data associated with Second Heading';
$row2 = new html_table_row();
$row2->cells = [$head2, $cell3, $cell4];
$table->data = [$row1, $row2];
echo html_writer::table($table);

echo $OUTPUT->heading('Turning off the default "generaltable" classes is not very easy, you have to'.
    ' override the class attribute on the table element. Note that setting any class, will by default'.
    ' remove the generaltable class', 4);

$table = new html_table();
$table->head = ['Name', 'Grade'];
$table->attributes = ['class' => ""]; // Need some other class stop the "generaltable" class
// but no easy way to do the same on the cells.
$table->head = ['Name', 'Grade'];
$table->data = [
    ['Joe Bloggs', '76%'],
    ['Jane Doe', '100%'],
];
echo html_writer::table($table);

echo $OUTPUT->heading('If you need a plain table, you can build it yourself using html_writer:', 4);

echo html_writer::start_tag('table');
echo html_writer::start_tag('tr');
echo html_writer::tag('th', 'Left Heading');
echo html_writer::tag('th', 'Right Heading');
echo html_writer::end_tag('tr');
echo html_writer::start_tag('tr');
echo html_writer::tag('td', 'Top Left');
echo html_writer::tag('td', 'Top Right');
echo html_writer::end_tag('tr');
echo html_writer::start_tag('tr');
echo html_writer::tag('td', 'Bottom Left');
echo html_writer::tag('td', 'Bottom Right');
echo html_writer::end_tag('tr');
echo html_writer::end_tag('table');

echo $OUTPUT->heading('A "flexible table" moodle table (class "flexible"). Useful if you want pagination, sorting etc.', 3);

require_once($CFG->libdir . '/tablelib.php');
$table = new flexible_table('test-flexible-table');
$table->define_baseurl($url);
$table->define_columns(['col1', 'col2']);
$table->define_headers(['Column 1', 'Column 2']);
$table->sortable(true, 'col1', SORT_ASC);
$table->setup();
$table->add_data(['Top Left', 'Top Right']);
$table->add_data(['Bottom Left', 'Bottom Right']);
$table->finish_html();

echo $OUTPUT->heading('By default tables are left aligned. Use the boxaligncenter class if you'.
    ' want the table to be centred instead', 4);

$table = new html_table();
$table->attributes['class'] = 'boxaligncenter';
$table->head = ['Name', 'Grade'];
$table->data = [
    ['Harry Potter', '76%'],
    ['Hermione Granger', '100%'],
];
echo html_writer::table($table);

echo $OUTPUT->heading('You can specify \'hr\' as a row to get a horizontal rule that spans all columns. Note'.
    ' this only works if you have the table \'head\' defined!', 4);

$table = new html_table();
$table->head = ['Name', 'Grade'];
$table->data = [
    ['Harry Potter', '76%'],
    ['Hermione Granger', '100%'],
    'hr',
    ['Harry Potter', '76%'],
    ['Hermione Granger', '100%'],
];
echo html_writer::table($table);


echo $OUTPUT->container_end();

echo $OUTPUT->box_end();

echo $OUTPUT->footer();

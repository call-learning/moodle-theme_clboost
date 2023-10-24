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
$strheading = 'Element Library: Tab bars';
$url = new moodle_url('/theme/clboost/tools/elementlibrary/tabs.php');

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

echo $OUTPUT->container_start();

$tabs = [];
$row = [];
$inactive = [];
$activated = [];

echo $OUTPUT->box('Standard, single row tab bar');

$url = new moodle_url('index.php');
$row[] = new tabobject('tab1',
    $url->out(),
    'Selected Tab',
    'This is the hover text for tab1'
);

$row[] = new tabobject('tab2',
    $url->out(),
    'Another tab',
    'This is the hover text for tab2'
);

$row[] = new tabobject('tab3',
    $url->out(),
    'Yet Another tab',
    'This is the hover text for tab3'
);

$row[] = new tabobject('tab4',
    $url->out(),
    'More tabs',
    'This is the hover text for tab4'
);

$row[] = new tabobject('tab5',
    $url->out(),
    'Even more tabs',
    'This is the hover text for tab5'
);

$currenttab = 'tab1';
$tabs[] = $row;
print_tabs($tabs, $currenttab, $inactive, $activated);
echo html_writer::tag('div',
    LOREMIPSUM);

echo $OUTPUT->box('Same as above, but with a couple of disabled tabs');

print_tabs($tabs, $currenttab, ['tab4', 'tab5'], $activated);

echo $OUTPUT->box('Same as above, but with a couple of extra activated tabs');

print_tabs($tabs, $currenttab, $inactive, ['tab4', 'tab5']);

echo $OUTPUT->box('You can set a flag on the tab object to keep the active tab as a link, though this doesn\'t affect'.
    ' additional selected tabs, just the current tab.');

$row = [];
$url = new moodle_url('index.php');
$row[] = new tabobject('tab1',
    $url->out(),
    'Selected Tab',
    'This is the hover text for tab1',
    true
);

$row[] = new tabobject('tab2',
    $url->out(),
    'Another tab',
    'This is the hover text for tab2',
    true
);

$row[] = new tabobject('tab3',
    $url->out(),
    'Yet Another tab',
    'This is the hover text for tab3',
    true
);

$row[] = new tabobject('tab4',
    $url->out(),
    'Activated tab',
    'This is the hover text for tab4',
    true
);

$row[] = new tabobject('tab5',
    $url->out(),
    'Another activated tab',
    'This is the hover text for tab5',
    true
);

$tabs2 = [$row];
print_tabs($tabs2, $currenttab, $inactive, ['tab4', 'tab5']);

echo $OUTPUT->box('Extra rows can be used to hold further subcategories.');

$row1 = [
    new tabobject('row1a', $url->out(), 'Activated Row 1 Tab A'),
    new tabobject('row1b', $url->out(), 'Row 1 Tab B'),
    new tabobject('row1c', $url->out(), 'Disabled Row 1 Tab C'),
];
$row2 = [
    new tabobject('row2a', $url->out(), 'Row 2 Tab A'),
    new tabobject('row2b', $url->out(), 'Selected Row 2 Tab B'),
    new tabobject('row2c', $url->out(), 'Row 2 Tab C'),
];
$tabs = [];
$tabs[] = $row1;
$tabs[] = $row2;
$currenttab = 'row2b';
$inactive = ['row1c'];
$activated = ['row1a'];
print_tabs($tabs, $currenttab, $inactive, $activated);

echo $OUTPUT->box('Extra rows can be used to hold further subcategories. You can nest as many levels of'.
    ' tabs as you like but for Totara please stick to a maximum of two levels, any more will be confusing anyway. '.
    'Note that while each row is specified independently in the code, the HTML that is rendered nests the list items '.
    'hierarchically, so the activated tabs on each row contains the list items for the level below.');

$row1 = [
    new tabobject('row1a', $url->out(), 'Activated Row 1 Tab A'),
    new tabobject('row1b', $url->out(), 'Row 1 Tab B'),
    new tabobject('row1c', $url->out(), 'Disabled Row 1 Tab C'),
];
$row2 = [
    new tabobject('row2a', $url->out(), 'Row 2 Tab A'),
    new tabobject('row2b', $url->out(), 'Selected Row 2 Tab B'),
    new tabobject('row2c', $url->out(), 'Row 2 Tab C'),
];
$tabs = [];
$tabs[] = $row1;
$tabs[] = $row2;
$currenttab = 'row2b';
$inactive = ['row1c'];
$activated = ['row1a'];
print_tabs($tabs, $currenttab, $inactive, $activated);
echo html_writer::tag('div',
    LOREMIPSUM);

echo $OUTPUT->box('Sometimes the tab bars can get very full, especially when translated into other languages.'.
    ' We need to ensure they are at least readable when the tabs are too wide for the page.');

$row1 = [
    new tabobject('row1a', $url->out(),
        'Row 1 Tab A. This is a really long title. This is a really long title. This is a really long title.'),
    new tabobject('row1b', $url->out(),
        'Row 1 Tab B. This is a really long title. This is a really long title. This is a really long title.'),
    new tabobject('row1c', $url->out(),
        'Row 1 Tab C. This is a really long title. This is a really long title. This is a really long title.'),
];
$row2 = [
    new tabobject('row2a', $url->out(),
        'Row 2 Tab A. This is a really long title. This is a really long title. This is a really long title.'),
    new tabobject('row2b', $url->out(),
        'Row 2 Tab B. This is a really long title. This is a really long title. This is a really long title.'),
    new tabobject('row2c', $url->out(),
        'Row 2 Tab C. This is a really long title. This is a really long title. This is a really long title.'),
];
$tabs = [];
$tabs[] = $row1;
$tabs[] = $row2;
$currenttab = 'row2b';
$inactive = ['row1c'];
$activated = ['row1a'];
print_tabs($tabs, $currenttab, $inactive, $activated);
echo html_writer::tag('div',
    'This is a div tag directly under an overflowing two row tab bar. Make sure the padding is correct.'
    .' Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed eu accumsan nulla. '.
    'Cras elementum tincidunt dictum. Phasellus varius, est non ornare mattis, leo velit congue libero, vitae suscipit ipsum '.
    'urna sed orci. Pellentesque venenatis pulvinar lobortis. Vestibulum iaculis commodo eros quis volutpat. '.
    'Morbi vitae dapibus ante. Nullam convallis interdum ipsum, venenatis consequat eros faucibus sed. Pellentesque '.
    'non tellus vel eros ullamcorper sollicitudin ut in lectus. Sed aliquet gravida porta.');

echo $OUTPUT->container_end();

echo $OUTPUT->box_end();

echo $OUTPUT->footer();

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

/**
 * Todo:
 *  - it would be useful to apply an incremental className to form groups, to ctrl
 *    spacing between stacked groups, eg; 'class="felement fgroup fgroup1"'
 **/

require_once(dirname(__FILE__) . '../../../../../config.php');
global $CFG, $PAGE, $OUTPUT;
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/lib/formslib.php');

$strheading = 'Element Library: Moodle Forms: Custom Forms';
$url = new moodle_url('/theme/clboost/tools/elementlibrary/mform_custom.php');

// Start setting up the page.
$params = [];
$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($strheading);
$PAGE->set_heading($strheading);

admin_externalpage_setup('clboostelementlibrary');

echo $OUTPUT->header();

echo html_writer::link(new moodle_url('mform.php'), '&laquo; Back to moodle forms');
echo $OUTPUT->heading($strheading);

echo $OUTPUT->box_start();
echo $OUTPUT->container_start();

echo $OUTPUT->heading('Tabular form (class "tabularform")', 3);

/**
 * Class tabular_form
 *
 * @package   theme_clboost
 * @copyright Simon Coggins
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tabular_form extends moodleform {

    /**
     * Define the form
     */
    public function definition() {

        $mform =& $this->_form;
        $renderer =& $mform->defaultRenderer();

        // A $renderer->clearAllTemplates()  might be useful here depending on requirements.

        // Lmitation - we can't use the 'html_writer:table()' or 'flexible_table()' methods?
        $theadrow = html_writer::tag('thead', html_writer::tag('tr',
            html_writer::tag('th', 'Select', ['class' => 'header c0', 'scope' => 'col']) .
            html_writer::tag('th', 'Item', ['class' => 'header c1', 'scope' => 'col'])));
        $templatewrap = html_writer::tag('table', $theadrow . '{content}', ['class' => 'generaltable fcontainer']);
        $templateelement = html_writer::tag('tr',
            html_writer::tag('td', '<!-- END error -->{element}', ['class' => 'felement']) .
            html_writer::tag('td', '<!-- BEGIN required -->' . $mform->getReqHTML() . '<!-- END required -->{label}',
                ['class' => 'fitemtitle']), ['class' => 'fitem']);

        $renderer->setGroupTemplate($templatewrap, 'tabular_checkboxes');
        $renderer->setGroupElementTemplate($templateelement, 'tabular_checkboxes');

        $controlgroup = [];
        $controlgroup[] =& $mform->createElement('checkbox', 'tabular_checkbox1', 'checkbox_name');
        $controlgroup[] =& $mform->createElement('checkbox', 'tabular_checkbox2', 'checkbox_name');
        $controlgroup[] =& $mform->createElement('checkbox', 'tabular_checkbox3', 'checkbox_name');
        $controlgroup[] =& $mform->createElement('checkbox', 'tabular_checkbox4', 'checkbox_name');
        $controlgroup[] =& $mform->createElement('checkbox', 'tabular_checkbox5', 'checkbox_name');

        $mform->addGroup($controlgroup, 'tabular_checkboxes', '', [' '], false);

        $mform->addElement('submit', 'submit_btn', 'Submit');

        $oldclass = $mform->getAttribute('class');
        if (!empty($oldclass)) {
            $mform->updateAttributes(['class' => $oldclass . ' tabularform']);
        } else {
            $mform->updateAttributes(['class' => 'tabularform']);
        }
    }

}

$form = new tabular_form();
$data = $form->get_data();
$form->display();

echo html_writer::empty_tag('hr');
echo $OUTPUT->heading('Form with grouped \'action\' controls above \'data\' controls (class "actionform"),' .
    ' by providing new template markup to the Renderer.',
    3);

/**
 * Class action_form
 *
 * @package   theme_clboost
 * @copyright Simon Coggins
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_form extends moodleform {
    /**
     * Define the form
     */
    public function definition() {

        $mform =& $this->_form;
        $renderer =& $mform->defaultRenderer();

        // All 'action' controls grouped together here.
        $templateawrap = html_writer::tag('div', '{content}', ['class' => 'fcontainer actionform']);
        $templateaelement = html_writer::tag('div',
            html_writer::tag('div', '<!-- BEGIN required -->' . $mform->getReqHTML() . '<!-- END required -->{label}',
                ['class' => 'fitemtitle']) .
            html_writer::tag('div', '<!-- END error -->{element}', ['class' => 'felement']), ['class' => 'fitem']);

        $renderer->setGroupTemplate($templateawrap, 'action_group');
        $renderer->setGroupElementTemplate($templateaelement, 'action_group');

        $group = [];
        $group[] =& $mform->createElement('date_time_selector', 'date_select',
            'Choose a date:'); // Grouped date_time_selector objects don't run the JS enhancement?
        $c1 = $mform->createElement('text', 'text_entry', 'Search term:');
        $mform->setType('text_entry', PARAM_INT);
        $group[] =& $c1;
        $c2 = $mform->createElement('checkbox', 'action_group_checkbox', 'checkbox_name');
        $group[] =& $c2;
        $group[] =&
            $mform->createElement('select', 'action_group_auth', 'select_menu_name', ['Option 1', 'Option 2', 'Option 3']);

        $mform->addGroup($group, 'action_group', '', [' '], false);
        $mform->addElement('submit', 'submit_btn_2', 'Submit');

        // Render the table containing more controls.
        $theadrow = html_writer::tag('thead', html_writer::tag('tr',
            html_writer::tag('th', 'Select', ['class' => 'header c0', 'scope' => 'col']) .
            html_writer::tag('th', 'Item', ['class' => 'header c1', 'scope' => 'col'])));
        $templatebwrap = html_writer::tag('table', $theadrow . '{content}', ['class' => 'generaltable fcontainer']);
        $templatebelement = html_writer::tag('tr',
            html_writer::tag('td', '<!-- END error -->{element}', ['class' => 'felement']) .
            html_writer::tag('td', '<!-- BEGIN required -->' . $mform->getReqHTML() . '<!-- END required -->{help}{label}',
                ['class' => 'fitemtitle']), ['class' => 'fitem']);

        $renderer->setGroupTemplate($templatebwrap, 'tabular_checkboxes_2');
        $renderer->setGroupElementTemplate($templatebelement, 'tabular_checkboxes_2');

        $group = [];
        $group[] =& $mform->createElement('checkbox', 'tabular_checkbox6', 'checkbox_name');
        $group[] =& $mform->createElement('checkbox', 'tabular_checkbox7', 'checkbox_name');
        $group[] =& $mform->createElement('checkbox', 'tabular_checkbox8', 'checkbox_name');
        $group[] =& $mform->createElement('checkbox', 'tabular_checkbox9', 'checkbox_name');
        $group[] =& $mform->createElement('checkbox', 'tabular_checkbox10', 'checkbox_name');

        $mform->addGroup($group, 'tabular_checkboxes_2', '', [' '], false);

    }
}

$form = new action_form(); // Shouldn't this have an 'id' attribute of 'tabular_form'? ref. /lib/formslib.php:123.
$data = $form->get_data();
$form->display();

echo html_writer::empty_tag('hr');
echo $OUTPUT->heading('Minimal forms', 3);

/**
 * Class minimal_form
 *
 * @package   theme_clboost
 * @copyright Simon Coggins
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class minimal_form extends moodleform {

    /**
     * Define the form
     */
    public function definition() {

        $mform =& $this->_form;

        switch ($this->_customdata['type']) {
            case 'search' :
                $mform->addElement('text', 'search');
                $mform->setType('search', PARAM_RAW);
                $mform->addElement('submit', 'search_btn', 'Go');
                break;
            case 'single_button' :
                $this->add_action_buttons(false, 'Turn editing on');
                break;
            default:
                break;
        }
    }
}

echo html_writer::empty_tag('hr');
$form = new minimal_form(null, ['type' => 'search']);
$data = $form->get_data();
$form->display();

echo html_writer::empty_tag('hr');
$form = new minimal_form(null, ['type' => 'single_button']);
$data = $form->get_data();
$form->display();

echo $OUTPUT->container_end();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();

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
require_once($CFG->dirroot . '/lib/formslib.php');

$strheading = 'Element Library: Moodle Forms: Standard elements';
$url = new moodle_url('/theme/clboost/tools/elementlibrary/mform_standard.php');

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
echo $OUTPUT->container('Examples of different types of elements. Submit the form to see server side validation message' .
    'for each item when validation fails.');
echo $OUTPUT->box_end();

/**
 * Class standard_form_elements
 *
 * @package   theme_clboost
 * @copyright Simon Coggins
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class standard_form_elements extends moodleform {

    /**
     * Form definitions
     */
    public function definition() {
        global $CFG;

        $mform =& $this->_form;

        $mform->addElement('header', null, 'Controls');

        $mform->addElement(
            'checkbox', 'disableelements', 'Disable all elements below', 'Use to test disabled state styles and functionality.');

        $mform->addElement('header', null, 'Header element');

        $mform->addElement('button', 'buttonfield', 'Button text');
        $mform->disabledIf('buttonfield', 'disableelements', 'checked');

        $mform->addElement('checkbox', 'checkboxfield', 'A checkbox', 'Label next to checkbox');
        $mform->disabledIf('checkboxfield', 'disableelements', 'checked');

        $this->add_checkbox_controller(1);
        $mform->addElement(
            'advcheckbox', 'advcheckboxfield1', 'Advanced checkbox 1', 'Label next to advanced checkbox 1', ['group' => 1]);
        $mform->addElement(
            'advcheckbox', 'advcheckboxfield2', 'Advanced checkbox 2', 'Label next to advanced checkbox 2', ['group' => 1]);
        $mform->addElement(
            'advcheckbox', 'advcheckboxfield3', 'Advanced checkbox 3', 'Label next to advanced checkbox 3', ['group' => 1]);
        $mform->disabledIf('advcheckboxfield1', 'disableelements', 'checked');
        $mform->disabledIf('advcheckboxfield2', 'disableelements', 'checked');
        $mform->disabledIf('advcheckboxfield3', 'disableelements', 'checked');

        $mform->addElement('static', 'datedes', '', 'Don\'t forget to style the popup dialog');
        $mform->addElement('date_selector', 'datefield', 'Date selector');
        $mform->disabledIf('datefield', 'disableelements', 'checked');

        $mform->addElement('static', 'datetimedes', '', 'Don\'t forget to style the popup dialog');
        $mform->addElement('date_time_selector', 'datetimefield', 'Date and time selector');
        $mform->disabledIf('datetimefield', 'disableelements', 'checked');

        $mform->addElement('duration', 'durationfield', 'Duration');
        $mform->disabledIf('durationfield', 'disableelements', 'checked');

        $mform->addElement('editor', 'editorfield', 'HTML Editor');
        $mform->disabledIf('editorfield', 'disableelements', 'checked');

        $mform->addElement('static', 'filepickerdes', '', 'Use the file picker to let the user choose or upload one or ' .
            'more files to be processed immediately (such as a CSV import).');
        $mform->addElement('filepicker', 'filepickerfield', 'Filepicker');
        $mform->disabledIf('filepickerfield', 'disableelements', 'checked');

        $mform->addElement('static', 'filemanagerdes', '', 'Use the file manager when the user needs to upload one or ' .
            'more files to the server. Remember to disabled subfolders if not required.');
        $mform->addElement('filemanager', 'filemanagerfield', 'Filemanager');
        $mform->disabledIf('filemanagerfield', 'disableelements', 'checked');

        $mform->addElement('modgrade', 'modgradefield', 'Mod grade');
        $mform->disabledIf('modgradefield', 'disableelements', 'checked');

        $mform->addElement('modvisible', 'modvisiblefield', 'Mod visible');
        $mform->disabledIf('modvisiblefield', 'disableelements', 'checked');

        $mform->addElement('password', 'passwordfield', 'Password field');
        $mform->disabledIf('passwordfield', 'disableelements', 'checked');

        $mform->addElement('passwordunmask', 'passwordunmaskfield', 'Password field with unmask option');
        $mform->disabledIf('passwordunmaskfield', 'disableelements', 'checked');

        $mform->addElement('static', 'radiodes', '', 'Usually radio buttons are grouped, see grouped page for details');
        $mform->addElement('radio', 'radiofield', 'Radio', 'Text to right of radio', 1);
        $mform->addElement('radio', 'radiofield', 'Radio 2', 'Text to right of radio 2', 2);
        $mform->disabledIf('radiofield', 'disableelements', 'checked');

        $mform->addElement('select', 'selectfield', 'Single select', [0 => 'Item 1', 1 => 'Item 2', 3 => 'Item 3']);
        $mform->disabledIf('selectfield', 'disableelements', 'checked');

        $mform->addElement('static', 'multiselectdesc', '', 'Use checkboxes instead of multiselects where possible. ' .
            'If you must use, include a label indicating ctrl can be used to select/unselect multiple items');
        $select = &$mform->addElement(
            'select', 'multiselectfield', 'Multi select', [0 => 'Item 1', 1 => 'Item 2', 3 => 'Item 3']);
        $select->setMultiple(true);
        $mform->disabledIf('multiselectfield', 'disableelements', 'checked');

        // To disable individual options, build the select manually.
        $select2 = $mform->createElement('select', 'selectwithdisabledoptionsfield', 'Select with disabled options');
        $select2->addOption('An active option', '');
        $select2->addOption('A disabled option', '', ['disabled' => 'disabled']);
        $select2->addOption('Another active option', '');
        $select2->addOption('Another disabled option', '', ['disabled' => 'disabled']);
        $mform->addElement($select2);
        $mform->disabledIf('selectwithdisabledoptionsfield', 'disableelements', 'checked');

        $groupselectoptions = [
            'group one' => [1 => 'one', 2 => 'two', 3 => 'three'],
            'group two' => [1 => 'one', 2 => 'two', 3 => 'three'],
            'group three' => [1 => 'one', 2 => 'two', 3 => 'three'],
        ];
        $mform->addElement('selectgroups', "groupedselectfield", 'Grouped select', $groupselectoptions);
        $mform->disabledIf('groupedselectfield', 'disableelements', 'checked');

        $mform->addElement('selectyesno', 'selectyesnofield', 'Yes/No select');
        $mform->disabledIf('selectyesnofield', 'disableelements', 'checked');

        $mform->addElement(
            'selectwithlink', 'selectwithlinkfield', 'Select with link', [1 => 'One', 2 => 'Two', 3 => 'Three'], null,
            ['link' => $CFG->wwwroot . '/theme/clboost/tools/elementlibrary/', 'label' => 'A label']);
        $mform->disabledIf('selectwithlinkfield', 'disableelements', 'checked');

        $mform->addElement(
            'searchableselector', 'searchableselectorfield', 'Searchable selector',
            get_string_manager()->get_list_of_countries(true));
        $mform->disabledIf('searchableselectorfield', 'disableelements', 'checked');

        $mform->addElement('submit', 'submitfield', 'Submit text');
        $mform->disabledIf('submitfield', 'disableelements', 'checked');
        $mform->addElement('reset', 'resetfield', 'Reset text');
        $mform->disabledIf('resetfield', 'disableelements', 'checked');
        $mform->addElement('cancel', 'cancelfield', 'Cancel text');
        $mform->disabledIf('cancelfield', 'disableelements', 'checked');

        // No 'size' attribute, use CSS definitions.
        $mform->addElement('text', 'textfield', 'Text field');
        $mform->setDefault('textfield', 'Default text');
        $mform->setType('textfield', PARAM_RAW);
        $mform->disabledIf('textfield', 'disableelements', 'checked');

        $mform->addElement('textarea', 'textareafield', 'Textarea field', 'wrap="virtual" rows="20" cols="50"');
        $mform->setDefault('textareafield', 'Default text');
        $mform->setType('textarea', PARAM_RAW);
        $mform->disabledIf('textareafield', 'disableelements', 'checked');

        $mform->addElement('static', 'tagsdesc', '',
            'The tags field has options to show just official tags, just user tags or both. Here is both:');
        $mform->addElement('tags', 'tagsfield', 'Tags');
        $mform->setType('tagsfield', PARAM_RAW);
        $mform->disabledIf('tagsfield', 'disableelements', 'checked');

        $mform->addElement('static', 'datedes', '', 'Don\'t forget to style the popup dialog');
        $mform->addElement('url', 'urlfield', 'URL');
        $mform->setType('urlfield', PARAM_RAW);
        $mform->disabledIf('urlfield', 'disableelements', 'checked');

        $mform->addElement('header', null, 'Another Header element');

        $mform->addElement('static', 'staticfield', 'A static element', 'A static field\'s value');

        $this->add_action_buttons(true, 'Submit button');
    }

    /**
     * Validate  form
     *
     * @param array $formelements
     * @param array $files
     * @return array
     */
    public function validation($formelements, $files) {
        $err = [];
        foreach ($formelements as $name => $value) {
            $err[$name] = 'Custom validation message for ' . $name;
        }
        // Some elements need to be manually failed as they aren't included
        // in the formelements array.
        $error = 'Custom validation message';
        $err['buttonfield'] = $error;
        $err['submitfield'] = $error;
        $err['resetfield'] = $error;
        $err['cancelfield'] = $error;
        $err['radiofield'] = $error;
        $err['searchableselectorfield'] = $error;
        $err['checkboxfield'] = $error;
        $err['multiselectfield'] = $error;
        $err['selectwithdisabledoptionsfield'] = $error;
        return $err;
    }
}

$form = new standard_form_elements();
$data = $form->get_data(); // Enables server validation.
$form->display();

echo $OUTPUT->footer();


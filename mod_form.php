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
 * @package    mod_datalynxcoursepage
 * @copyright  2015 David Bogner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Configure the instance settings.
 */
class mod_datalynxcoursepage_mod_form extends moodleform_mod {

    public function definition() {
        global $DB, $SITE;
        $mform = $this->_form;

        // Fields for editing HTML block title and contents.
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $this->standard_intro_elements();

        // Datalynx menu.
        $options = array(0 => get_string('choosedots'));
        $courseids = array($SITE->id, $this->current->course);
        list($insql, $params) = $DB->get_in_or_equal($courseids);
        if ($datalynxs = $DB->get_records_select_menu('datalynx', " course $insql ", $params, 'name', 'id,name')) {
            foreach ($datalynxs as $key => $value) {
                $datalynxs[$key] = strip_tags(format_string($value, true));
            }
            $options = $options + $datalynxs;
        }
        $mform->addElement('select', 'datalynx', get_string('selectdatalynx', 'datalynxcoursepage'), $options);

        // Views menu.
        $options = array(0 => get_string('choosedots'));
        $mform->addElement('select', "view", get_string('selectview', 'datalynxcoursepage'), $options);
        $mform->disabledIf("view", "datalynx", 'eq', 0);

        $this->standard_coursemodule_elements();

        // Buttons.
        $this->add_action_buttons(true, false, null);
    }

    /**
     * Definition after data
     *
     * @return void
     * @throws dml_exception
     */
    public function definition_after_data() {
        global $DB;

        if ($selectedarr = $this->_form->getElement('datalynx')->getSelected()) {
            $datalynxid = reset($selectedarr);
        } else {
            $datalynxid = 0;
        }

        if ($selectedarr = $this->_form->getElement('view')->getSelected()) {
            $viewid = reset($selectedarr);
        } else {
            $viewid = 0;
        }

        if ($datalynxid) {
            if ($views = $DB->get_records_menu('datalynx_views', array('dataid' => $datalynxid), 'name', 'id,name')) {
                $configview = &$this->_form->getElement('view');
                foreach ($views as $key => $value) {
                    $configview->addOption(strip_tags(format_string($value, true)), $key);
                }
            }
        }
    }

    /**
     * Data processing
     * @param $data
     * @return void
     */
    public function data_preprocessing(&$data) {
        $data = (array) $data;
        parent::data_preprocessing($data);
    }

    /**
     * Set form data
     *
     * @param $data
     * @return void
     */
    public function set_data($data) {
        $this->data_preprocessing($data);
        parent::set_data($data);
    }

    /**
     * Return the data
     *
     * @return false|object
     */
    public function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return false;
        }
        return $data;
    }

    /**
     * Data validation
     * @param $data
     * @param $files
     * @return array
     * @throws coding_exception
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $errors = array();
        if (!empty($data['datalynx']) && empty($data['view'])) {
            $errors['view'] = get_string('missingview', 'datalynxcoursepage');
        }
        return $errors;
    }
}

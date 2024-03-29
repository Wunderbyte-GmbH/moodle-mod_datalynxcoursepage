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
 * @package mod_datalynxcoursepage
 * @copyright 2012 Itamar Tzadok
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Structure step to restore one datalynxcoursepage activity
 */
class restore_datalynxcoursepage_activity_structure_step extends restore_activity_structure_step {

    /**
     * Define structure
     * @return mixed
     */
    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('datalynxcoursepage', '/activity/datalynxcoursepage');

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Processs data
     * @param $data
     * @return void
     * @throws base_step_exception
     * @throws dml_exception
     */
    protected function process_datalynxcoursepage($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // Insert the datalynxcoursepage record.
        $newitemid = $DB->insert_record('datalynxcoursepage', $data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Post restore procedure
     * @return void
     */
    protected function after_execute() {
        // Add datalynxcoursepage related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_datalynxcoursepage', 'intro', null);
    }

}

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
 * @copyright  2012 Itamar Tzadok
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define the complete datalynxcoursepage structure for backup, with file and id annotations.
 */
class backup_datalynxcoursepage_activity_structure_step extends backup_activity_structure_step {

    /**
     * Define backup structure
     *
     * @return backup_nested_element
     * @throws base_element_struct_exception
     * @throws base_step_exception
     */
    protected function define_structure() {

        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $datalynxcoursepage = new backup_nested_element('datalynxcoursepage', array('id'), array(
            'name', 'intro', 'introformat', 'timemodified', 'datalynx', 'view', 'embed'));

        // Build the tree.
        // (None).

        // Define sources.
        $datalynxcoursepage->set_source_table('datalynxcoursepage', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations.
        // (None).

        // Define file annotations.
        $datalynxcoursepage->annotate_files('mod_datalynxcoursepage', 'intro', null); // This file area hasn't itemid.

        // Return the root element (datalynxcoursepage), wrapped into standard activity structure.
        return $this->prepare_activity_structure($datalynxcoursepage);
    }
}

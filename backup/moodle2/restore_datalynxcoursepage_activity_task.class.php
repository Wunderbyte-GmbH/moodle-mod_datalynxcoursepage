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
 * @package mod
 * @subpackage datalynxcoursepage
 * @copyright 2012 Itamar Tzadok
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once "$CFG->dirroot/mod/datalynxcoursepage/backup/moodle2/restore_datalynxcoursepage_stepslib.php";

/**
 * datalynxcoursepage restore task that provides all the settings and steps to perform one
 * complete restore of the activity.
 */
class restore_datalynxcoursepage_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have.
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Datalynx embed only has one structure step.
        $this->add_step(new restore_datalynxcoursepage_activity_structure_step('datalynxcoursepage_structure',
            'datalynxcoursepage.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     *
     * @return array
     */
    public static function define_decode_contents() : array {
        $contents = array();
        $contents[] = new restore_decode_content('datalynxcoursepage', array('intro'), 'datalynxcoursepage');
        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder.
     *
     * @return array
     */
    public static function define_decode_rules() : array {
        return array();
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * datalynxcoursepage logs. It must return one array
     * of {@link restore_log_rule} objects.
     * @return array
     */
    static public function define_restore_log_rules() : array {
        $rules = array();
        $rules[] = new restore_log_rule('datalynxcoursepage', 'add', 'view.php?id={course_module}', '{datalynxcoursepage}');
        $rules[] = new restore_log_rule('datalynxcoursepage', 'update', 'view.php?id={course_module}', '{datalynxcoursepage}');
        $rules[] = new restore_log_rule('datalynxcoursepage', 'view', 'view.php?id={course_module}', '{datalynxcoursepage}');
        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects.
     *
     * Note these rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     * @return array
     */
    public static function define_restore_log_rules_for_course() : array {
        $rules = array();
        $rules[] = new restore_log_rule('datalynxcoursepage', 'view all', 'index.php?id={course}', null);
        return $rules;
    }

    /**
     * This function, executed after all the tasks in the plan
     * have been executed, will perform the recode of the
     * references to the datalynx instance. This must be done here
     * and not in normal execution steps because the datalynx instance
     * can be restored after this module has been restored.
     */
    public function after_restore() {
        global $DB;

        // Get the activityid.
        $activityid = $this->get_activityid();

        // Extract block configdata and update it to point to the new datalynx instance.
        if ($configdata = $DB->get_record('datalynxcoursepage', array('id' => $activityid))) {
            if (!empty($configdata->datalynx)) {
                // Get quiz mapping and replace it in config.
                if ($datamap = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'datalynx', $configdata->datalynx)) {
                    $DB->set_field('datalynxcoursepage', 'datalynx', $datamap->newitemid, array('id' => $activityid));
                }
            }
        }
        if (!empty($configdata->view)) {
            if ($datamap = restore_dbops::get_backup_ids_record($this->get_restoreid(), 'datalynx_view', $configdata->view)) {
                $DB->set_field('datalynxcoursepage', 'view', $datamap->newitemid, array('id' => $activityid));
            }
        }

    }
}

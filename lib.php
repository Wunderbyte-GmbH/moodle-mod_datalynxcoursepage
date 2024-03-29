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
 * @copyright  2014 onwards David Bogner
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_datalynx\datalynx;

/**
 * Add the activity instance
 *
 * @param $datalynxcoursepage
 * @return bool|int
 * @throws coding_exception
 * @throws dml_exception
 */
function datalynxcoursepage_add_instance($datalynxcoursepage) {
    global $DB;
    $datalynxcoursepage->name = get_string('modulename', 'datalynxcoursepage');
    $datalynxcoursepage->timemodified = time();
    return $DB->insert_record("datalynxcoursepage", $datalynxcoursepage);
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $datalynxcoursepage
 * @return bool
 */
function datalynxcoursepage_update_instance($datalynxcoursepage) {
    global $DB;
    $datalynxcoursepage->name = get_string('modulename', 'datalynxcoursepage');
    $datalynxcoursepage->timemodified = time();
    $datalynxcoursepage->id = $datalynxcoursepage->instance;
    if (empty($datalynxcoursepage->datalynx)) {
        $datalynxcoursepage->view = 0;
    }
    return $DB->update_record("datalynxcoursepage", $datalynxcoursepage);
}

/**
 * Given the id of a datalynxcoursepage instance, the instance will be deleted
 *
 * @param int $id
 * @return bool
 */
function datalynxcoursepage_delete_instance($id) {
    global $DB;

    if (!$datalynxcoursepage = $DB->get_record("datalynxcoursepage", array('id' => $id))) {
        return false;
    }
    $result = true;
    if (!$DB->delete_records("datalynxcoursepage", array('id' => $datalynxcoursepage->id))) {
        $result = false;
    }
    return $result;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 * See get_array_of_activities() in course/lib.php
 *
 * @param object $coursemodule
 * @return object|null
 */
function datalynxcoursepage_get_coursemodule_info($coursemodule) {
    global $DB;

    $fields = 'id, name, datalynx, view';
    if ($datalynxcoursepage = $DB->get_record('datalynxcoursepage', array('id' => $coursemodule->instance), $fields)) {
        if (empty($datalynxcoursepage->name)) {
            // Datalynx embed name missing, fix it.
            $datalynxcoursepage->name = "datalynxcoursepage{$datalynxcoursepage->id}";
            $DB->set_field('datalynxcoursepage', 'name', $datalynxcoursepage->name, array('id' => $datalynxcoursepage->id));
        }
        $info = new stdClass();
        $info->extra = '';
        $info->name = $datalynxcoursepage->name;
        return $info;
    }

    return null;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 * See get_array_of_activities() in course/lib.php
 *
 * @param cm_info $cm
 * @return void
 * @throws coding_exception
 * @throws dml_exception
 * @throws moodle_exception
 */
function datalynxcoursepage_cm_info_view(cm_info $cm) {
    global $DB, $CFG, $PAGE;
    require_once("{$CFG->dirroot}/mod/datalynx/locallib.php");

    $fields = 'id, name, intro, datalynx, view, embed';
    if (!$datalynxcoursepage = $DB->get_record('datalynxcoursepage', array('id' => $cm->instance), $fields)) {
        return;
    }

    // We must have at least datalynx id and view id.
    if (empty($datalynxcoursepage->datalynx) || empty($datalynxcoursepage->view)) {
        return;
    }

    // Sanity check in case the designated datalynx has been deleted.
    if ($datalynxcoursepage->datalynx && !$DB->record_exists('datalynx', array('id' => $datalynxcoursepage->datalynx))) {
        $content = get_string('datalynxinstance_deleted', 'mod_datalynxcoursepage');
        $cm->set_content($content);
        return;
    }

    // Sanity check in case the designated view has been deleted.
    if ($datalynxcoursepage->view && !$DB->record_exists('datalynx_views',
            array('dataid' => $datalynxcoursepage->datalynx, 'id' => $datalynxcoursepage->view))) {
        $content = get_string('datalynxview_deleted', 'mod_datalynxcoursepage');
        $cm->set_content($content);
        return;
    }

    // Sanity check if datalynx instance is in the same course as datalynxcoursepage.
    if (empty(get_fast_modinfo($cm->course)->instances['datalynx'][$datalynxcoursepage->datalynx])) {
        $content = get_string('datalynxinstance_missing', 'mod_datalynxcoursepage');
        $cm->set_content($content);
        return;
    }

    $jsurl = new moodle_url('/mod/datalynxcoursepage/js.php', array('id' => $cm->id));
    $PAGE->requires->js($jsurl);

    $datalynxid = $datalynxcoursepage->datalynx;
    $viewid = $datalynxcoursepage->view;
    $content = $datalynxcoursepage->intro;
    $content .= datalynx::get_content_inline($datalynxid, $viewid);

    if (!empty($content)) {
        $cm->set_content($content);
    }
}

/**
 * Get view actions
 * @return array
 */
function datalynxcoursepage_get_view_actions() {
    return array();
}

/**
 * Get post actions
 * @return array
 */
function datalynxcoursepage_get_post_actions() {
    return array();
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 *
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function datalynxcoursepage_reset_userdata($data) {
    return array();
}

/**
 * Returns all other caps used in module
 *
 * @return array
 */
function datalynxcoursepage_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * Supports these Moodle features
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return bool|null True if module supports feature, false if not, null if doesn't know
 * @uses FEATURE_IDNUMBER
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_GROUPMEMBERSONLY
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 */
function datalynxcoursepage_supports($feature) {
    switch ($feature) {
        case FEATURE_GROUPS:
        case FEATURE_GROUPINGS:
        case FEATURE_COMPLETION_TRACKS_VIEWS:
        case FEATURE_GRADE_OUTCOMES:
        case FEATURE_GRADE_HAS_GRADE:
        case FEATURE_IDNUMBER:
            return false;
        case FEATURE_BACKUP_MOODLE2:
        case FEATURE_NO_VIEW_LINK:
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_RESOURCE;

        default:
            return null;
    }
}

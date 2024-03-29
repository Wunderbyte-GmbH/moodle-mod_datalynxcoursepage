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
 * Basic JS to include custom CSS from datalynx instance
 *
 * @package mod_datalynxcoursepage
 * @copyright 2015 David Bogner
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once("../../config.php");

$id = required_param('id', PARAM_INT);
if (!$cm = get_coursemodule_from_id('datalynxcoursepage', $id)) {
    throw new \moodle_exception('invalidcoursemodule');
}
if (!$course = $DB->get_record("course", array('id' => $cm->course))) {
    throw new \moodle_exception('coursemisconf');
}
require_login($course, true, $cm);

$javascript = '';
$cssurl = new moodle_url ('/mod/datalynx/css.php', array(
    'd' => $cm->instance,
    'cssedit' => 0
));
$javascript = 'let link = document.createElement("link");
				link.rel = "stylesheet";
				link.type = "text/css";
				link.href = "' . $cssurl . '";
				document.getElementsByTagName("head")[0].appendChild(link);
				';

header('Content-Type: application/javascript');
echo $javascript;

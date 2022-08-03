<?php
// This file is part of Moodle - http://moodle.org/.
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
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Upgrade Moodle
 *
 * @param $oldversion
 * @return bool
 * @throws downgrade_exception
 * @throws upgrade_exception
 */
function xmldb_datalynxcoursepage_upgrade($oldversion) {

    if ($oldversion < 2015080617) {
        upgrade_mod_savepoint(true, 2015080617, 'datalynxcoursepage');
    }
    return true;
}

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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @package mod
 * @subpackage datalynx
 * @copyright 2015 David Bogner
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *         
 *          The Datalynx has been developed as an enhanced counterpart
 *          of Moodle's Database activity module (1.9.11+ (20110323)).
 *          To the extent that Datalynx code corresponds to Database code,
 *          certain copyrights on the Database module may obtain.
 */
require_once ('../../config.php');

$d = required_param ( 'd', PARAM_INT ); // datalynx id

$javascript = '';
$cssurl = new moodle_url ( '/mod/datalynx/css.php', array (
		'd' => $d,
		'cssedit' => 0 
) );
$javascript = 	'var link = document.createElement("link");
				link.rel = "stylesheet";
				link.type = "text/css";
				link.href = "' . $cssurl . '";
				document.getElementsByTagName("head")[0].appendChild(link);
				';
echo $javascript;
		
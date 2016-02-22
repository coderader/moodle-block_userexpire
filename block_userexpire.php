<?php //$Id: block_userexpire.php,v 1.0 2016-02-22 22:00:00 jrader Exp $

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
 *
 * @package    moodlecore
 * @subpackage block
 * @copyright  2012 Jeff Rader - Sunset Online
 * @author     2012 Jeff Rader <jrader@sibi.cc>
 * @version    1.0
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_userexpire extends block_base {

    function init() {
        $this->title = get_string('pluginname','block_userexpire');
    }

    function get_content() {
        global $CFG, $OUTPUT, $USER, $course, $DB;
        
        require_once($CFG->dirroot.'/message/lib.php');
        
        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';

        if (isloggedin() && is_object($course)) {
			if ($course->id != SITEID) {
				$sql = 'SELECT ue.id, ue.timestart, ue.timeend
					FROM mdl_user_enrolments ue
					JOIN mdl_enrol e on ue.enrolid = e.id
					WHERE ue.userid = ? AND e.courseid = ?';
			   
				$records = $DB->get_records_sql($sql, array($USER->id, $course->id));
				$student = reset($records);
				if (isset($student->timeend) && $student->timeend>0) {
					$this->title = get_string('expiretitle', 'block_userexpire');
					$this->content->text .= get_string('expirelabel', 'block_userexpire').": ".date(get_string('strftimedate', 'block_userexpire'),$student->timeend);
					$this->content->text .= " (".floor(($student->timeend - time())/(24*60*60))." ".get_string('expireday', 'block_userexpire').", ".
						floor((($student->timeend - time())%(24*60*60))/3600)." ".get_string('expirehours', 'block_userexpire').")";
				} else {
					$this->title = get_string('enrolltitle', 'block_userexpire');
					$link = new moodle_url('/enrol/index.php', array('id' => $course->id));
					$this->content->text .= '<a href="'.$link->out().'">'.get_string('enrolltext', 'block_userexpire').'</a>';
				}
			}
       } 

        $this->content->footer = '';
        return $this->content;
    }
    
}
?>
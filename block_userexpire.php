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
 * Language strings for user expire block.
 * 
 * This block shows when the user will expire in the course.
 * 
 * @package    block_userexpire
 * @copyright  2026 Jeff Rader - Sunset Online
 * @author     Jeff Rader <jrader@sibi.cc>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * The only class for the plugin.
 *
 * @package    mod_userexpire
 * @copyright  2026 Jeff Rader
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_userexpire extends block_base {
	/**
    * Standard title for the block
    *
    * @return string of the plugin name
    */
    public function init() {
        $this->title = get_string('pluginname', 'block_userexpire');
    }
    /**
    * The main output of the block.
    *
    * @return string of the output for the block
    */
    public function get_content() {
        global $CFG, $OUTPUT, $USER, $course, $DB;
        require_once($CFG->dirroot . '/message/lib.php');
        if ($this->content !== null) {
            return $this->content;
        }
        $this->content = new stdClass();
        $this->content->text = '';
        if (isloggedin() && is_object($course)) {
            if ($course->id != SITEID) {
                $sql = "SELECT ue.id, ue.timestart, ue.timeend
                    FROM {user_enrolments} ue
                    JOIN {enrol} e on ue.enrolid = e.id
                    WHERE ue.userid = ? AND e.courseid = ?";
                $records = $DB->get_records_sql($sql, [$USER->id, $course->id]);
                $student = reset($records);
                if (isset($student->timeend) && $student->timeend > 0) {
                    $this->title = get_string('expiretitle', 'block_userexpire');
                    $text = get_string('expirelabel', 'block_userexpire') . ": " .
                        date(get_string('strftimedate', 'block_userexpire'), $student->timeend) .
                        " (" . floor(($student->timeend - time()) / (24 * 60 * 60)) .
                        " " . get_string('expireday', 'block_userexpire') . ", " .
                        floor((($student->timeend - time()) % (24 * 60 * 60)) / 3600) . " " .
                        get_string('expirehours', 'block_userexpire') . ")";
                    $this->content->text = html_writer::tag('div', $text);
                } else {
                    $this->title = get_string('enrolltitle', 'block_userexpire');
                    $this->content->text = html_writer::link(
						new moodle_url($CFG->wwwroot . '/enrol/index.php',
						['id' => $course->id]), get_string('enrolltext', 'block_userexpire')
                    );
                }
            }
        }

        $this->content->footer = '';
        return $this->content;
    }
    /**
    * All of the formats of this block.
    *
    * @return array to designate where the block anca be used.
    */
    public function applicable_formats() {
    // Default case: the block can be used in courses and site index, but not in activities.
        return [
            'site-index' => false,
            'course-view' => true,
            'mod' => false,
        ];
    }
}

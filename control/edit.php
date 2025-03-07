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
 * Version details.
 *
 * @package    local_control
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/control/classes/form/edit.php');

global $DB, $PAGE, $USER, $CFG;

$PAGE->set_url(new moodle_url('/local/control/edit.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title("Parents Signup");

$user_role = $DB->get_record('role_assignments', array('userid' => $USER->id));
$role_id = $user_role->roleid;

// Execute SQL command to set default value for the 'verify' field
$sql = "ALTER TABLE {parents_login} MODIFY COLUMN verify TEXT DEFAULT '0'";
$DB->execute($sql);

if ($role_id == 5) {
    require_login();

    $mform = new parents_signup();

    echo $OUTPUT->header();

    if (isset($_SESSION['user_count'])) {
        if ($_SESSION['user_count'] == 1) {
            redirect(new moodle_url('/local/control/manage.php'));
        }
    }

    if ($mform->is_cancelled()) {
        redirect($CFG->wwwroot . '/local/control/manage.php', 'You redirected to another page');
    } elseif ($fromform = $mform->get_data()) {
        $code = str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
        $recordtoinsert = new stdClass();
        $hashed_password = password_hash($fromform->password, PASSWORD_DEFAULT);
        $recordtoinsert->username = $fromform->username;
        $recordtoinsert->password = $hashed_password;
        $recordtoinsert->full_name = $fromform->full_name;
        $recordtoinsert->student_id = $fromform->student_id;
        $recordtoinsert->email = $fromform->email;
        $recordtoinsert->code = $code;

        $DB->insert_record('parents_login', $recordtoinsert);

        redirect(new moodle_url('/local/control/login.php'));

        // sendMail();
    } else {
        $mform->display();
    }

    echo $OUTPUT->footer();
} else {
    $message = "You are not a student to be logged into the Parent Control Plugin";
    \core\notification::error($message);
    redirect(new moodle_url('/'));
}

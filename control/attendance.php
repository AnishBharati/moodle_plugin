<?php
require_once(__DIR__ . '/../../config.php');

echo $OUTPUT->header();


$PAGE->set_url(new moodle_url('/local/control/attendance.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title("Attendance Details");

if (!is_user_authenticated()) {
    // User is not authenticated, redirect to login page
    redirect(new moodle_url('/local/control/login.php'));
}

$attendanceDataFromDB = $DB->get_records('attendance_log', array('studentid' => $USER->id));
$user_role = $DB->get_record('role_assignments', array('userid' => $USER->id));
$role_id = $user_role->roleid;

if ($role_id == 5) {
    // Prepare an array to store attendance data for the Mustache template
    $attendanceData = array();

    foreach ($attendanceDataFromDB as $log) {
        // Convert timestamp to a readable date format
        $dateTaken = date('Y-m-d', $log->timetaken);

        // Fetch the corresponding session information from mdl_attendance_sessions
        $session = $DB->get_record('attendance_sessions', array('id' => $log->sessionid));

        // Check if the session is found
        if ($session) {
            // Fetch the corresponding attendance information from mdl_attendance
            $attendance = $DB->get_record('attendance', array('id' => $session->attendanceid));

            // Check if the attendance information is found
            if ($attendance) {
                // Convert session date timestamp to a readable date format
                $date = date('Y-m-d', $session->sessdate);

                $course_id = $DB->get_record('course', array('id' => $attendance->course));

                // Fetch the corresponding status description from mdl_attendance_statuses
                $statusDescription = '';
                $status = $DB->get_record('attendance_statuses', array('id' => $log->statusid));

                // Check if the status information is found
                if ($status) {
                    $statusDescription = $status->description;
                } else {
                }

                // Add the data to the array
                $attendanceData[] = array(
                    'date' => $date,
                    'attendance' => $statusDescription,
                    'remarks' => $log->remarks,
                    'courses' => $course_id->shortname,
                );
            } else {
            }
        } else {
        }
    }

    // Prepare data for Mustache template
    $templatecontext['attendanceData'] = $attendanceData;
    include(__DIR__ . '/navigation.php');

    // Render the attendance table using Mustache
    echo $OUTPUT->render_from_template('local_control/attendance', $templatecontext);

    echo $OUTPUT->footer();
} else {
    $message = "You are not a student to be logged into the Parent Control Plugin";
    \core\notification::error($message);
    redirect(new moodle_url('/'));
}

function is_user_authenticated()
{
    // Return true if authenticated, false otherwise
    return isset($_SESSION['user_count']) && $_SESSION['user_count'] === 1;
}

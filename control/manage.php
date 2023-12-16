<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/control/classes/form/login.php');

global $PAGE, $OUTPUT;

$PAGE->set_url(new moodle_url('/local/control/manage.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title("Control");

// Custom authentication check
if (!is_user_authenticated()) {
    // User is not authenticated, redirect to login page
    redirect(new moodle_url('/local/control/login.php'));
}

// Handle logout
if (optional_param('logout', 0, PARAM_BOOL)) {

    // Decrement user count
    $_SESSION['user_count'] = isset($_SESSION['user_count']) ? max(0, $_SESSION['user_count'] - 1) : 0;

    // Redirect to login page after logout
    redirect(new moodle_url('/local/control/login.php'));
}

$user_role = $DB->get_record('role_assignments', array('userid' => $USER->id));

$role_id = $user_role->roleid;

if ($role_id == 5) {
    echo $OUTPUT->header();

    // Display the logout button
    echo '<div style="text-align: right;">';
    echo '<form method="post" action="' . new moodle_url('/local/control/manage.php') . '">';
    echo '<input type="hidden" name="logout" value="1">';
    echo '<input type="submit" value="Logout">';
    echo '</form>';
    echo '</div>';

    // user is recognized by the user id 
    $user = $DB->get_record('user', array('id' => $USER->id));
    $name = $user->firstname . ' ' . $user->lastname;
    $email = $user->email;
    $id = $USER->id;

    $user_courses = $DB->get_records('user_enrolments', array('userid' => $USER->id));

    //value of name and email are sent to mustache file
    $templatecontext['name'] = $name;
    $templatecontext['email'] = $email;
    $templatecontext['id'] = $id;

    echo $OUTPUT->render_from_template('local_control/manage', $templatecontext);

    $user_courses = $DB->get_records('user_enrolments', array('userid' => $USER->id));

    echo '<style>';
    echo '.course-cards { display: flex; flex-wrap: wrap; justify-content: space-around; }';
    echo '.course-card { width: 300px; padding: 20px; margin: 20px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }';
    echo '.course-card h3 { font-size: 1.5em; margin-bottom: 10px; }';
    echo '.course-card p { color: #555; }';
    echo '.course-card a { display: block; margin-top: 15px; color: #007bff; text-decoration: none; }';
    echo '</style>';

    echo '<br>';
    echo '<h3 style="text-align: center;">List of Enrolled Courses</h3>';

    if (!empty($user_courses)) {
        echo '<div class="course-cards">';
        foreach ($user_courses as $enrolment) {
            // Fetch course information using enrol ID
            $enrol_course = $DB->get_record('enrol', array('id' => $enrolment->enrolid));

            if ($enrol_course) {
                // Fetch course information using course ID
                $course = $DB->get_record('course', array('id' => $enrol_course->courseid));

                if ($course) {
                    // Display course card
                    echo '<div class="course-card">';
                    echo '<h3>' . $course->fullname . '</h3>';
                    echo '<p>' . $course->shortname . '</p>';
                    echo '<a href="' . new moodle_url('/course/view.php', array('id' => $course->id)) . '">Go to Course</a>';
                    echo '</div>';
                } else {
                    echo '<p>Course information not found.</p>';
                }
            } else {
                echo '<p>Enrolment information not found.</p>';
            }
        }
        echo '</div>';
    } else {
        echo '<p>You are not enrolled in any courses.</p>';
    }

    $template_data = array(); // Array to store assignment data

    if (!empty($user_courses)) {
        foreach ($user_courses as $enrolments) {
            $enrol_course = $DB->get_record('enrol', array('id' => $enrolments->enrolid));

            $assignments = $DB->get_records('assign', array('course' => $enrol_course->courseid));

            if ($assignments) {
                foreach ($assignments as $assign_id) {
                    $assign_name = $assign_id->name;

                    $due_date = date('Y-m-d', $assign_id->duedate);

                    // Check submission status
                    $assign_status = $DB->get_record('assign_submission', array('assignment' => $assign_id->id, 'userid' => $USER->id));

                    $course = $DB->get_record('course', array('id' => $enrol_course->courseid));


                    $data = array(
                        'course_name' => $course->fullname,
                        'assignment_name' => $assign_name,
                        'submission_status' => ($assign_status && $assign_status->status == "submitted") ? "You have submitted" : 'You have not submitted',
                        'due_date' => $due_date,
                    );

                    // Check grades if submitted
                    if ($assign_status && $assign_status->status === "submitted") {
                        $assign_grades = $DB->get_record('assign_grades', array('assignment' => $assign_status->assignment, 'userid' => $USER->id));

                        $role = $DB->get_record('role_assignments', array('id' => $assign_grades->grader));

                        $teacher = $DB->get_record('user', array('id' => $role->userid));

                        $name = $teacher->firstname . ' ' . $teacher->lastname;
                        $assi_grade = $assign_grades->grade;

                        $data['grade'] = ($assign_grades) ? $assign_grades->grade : 'Not graded';
                        if ($assi_grade) {
                            $data['teacher_name'] = $name;
                        }
                    } else {
                        $data['grade'] = ''; // No grade if not submitted
                    }
                    $template_data[] = $data;
                }
            }
        }
    } else {
        echo '<p>You are not enrolled in any course.</p>';
    }

    // Send assignment data to the template file
    $templatecontext['assignments'] = $template_data;

    echo $OUTPUT->render_from_template('local_control/course', $templatecontext);


    // Fetch attendance data from the database based on the user ID
    $attendanceDataFromDB = $DB->get_records('attendance_log', array('studentid' => $USER->id));

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
                    // Handle the case where status information is not found
                    // (You may log an error or provide a default value)
                }

                // Add the data to the array
                $attendanceData[] = array(
                    'date' => $date,
                    'attendance' => $statusDescription, // Use the status description instead of statusid
                    'remarks' => $log->remarks, // Replace with the actual remarks data
                    'courses' => $course_id->shortname, // Replace with the actual field name for course name
                );
            } else {
                // Handle the case where attendance information is not found
                // (You may log an error or provide a default value)
            }
        } else {
            // Handle the case where session information is not found
            // (You may log an error or provide a default value)
        }
    }

    // Prepare data for Mustache template
    $templatecontext['attendanceData'] = $attendanceData;

    // Render the attendance table using Mustache
    echo $OUTPUT->render_from_template('local_control/attendance', $templatecontext);


} else {
    $message = "You are not a student to be logged into the Parent Control Plugin";
    \core\notification::error($message);
    redirect(new moodle_url('/'));
}
include(__DIR__ . '/calen.php');
echo $OUTPUT->footer();

function is_user_authenticated()
{
    // Return true if authenticated, false otherwise
    return isset($_SESSION['user_count']) && $_SESSION['user_count'] === 1;
}

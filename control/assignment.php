<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/control/classes/form/login.php');

global $DB, $PAGE, $OUTPUT, $USER;

$PAGE->set_url(new moodle_url('/local/control/assignment.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title("Assignment Details");

if (!is_user_authenticated()) {
    // User is not authenticated, redirect to login page
    redirect(new moodle_url('/local/control/login.php'));
}

$user_courses = $DB->get_records('user_enrolments', array('userid' => $USER->id));


$user_role = $DB->get_record('role_assignments', array('userid' => $USER->id));
$role_id = $user_role->roleid;

if ($role_id == 5) {
    require_login();

    echo $OUTPUT->header();

    include(__DIR__ . '/navigation.php');

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
                        $data['grade'] = '';
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

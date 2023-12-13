<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/control/classes/form/login.php');

global $PAGE, $OUTPUT;

$PAGE->set_url(new moodle_url('/local/control/manage.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title("Control");

echo $OUTPUT->header();

// User is recognized by the user id 
$user = $DB->get_record('user', array('id' => $USER->id));
$name = $user->firstname . ' ' . $user->lastname;
$email = $user->email;
$id = $USER->id;

$user_courses = $DB->get_records('user_enrolments', array('userid' => $USER->id));

// Values of name and email are sent to the mustache file
$templatecontext['name'] = $name;
$templatecontext['email'] = $email;
$templatecontext['id'] = $id;

echo $OUTPUT->render_from_template('local_control/manage', $templatecontext);

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
                    $data['grade'] = ($assign_grades) ? $assign_grades->grade : 'Not graded';
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

// Debugging: Output the template data
echo '<pre>';
print_r($template_data);
echo '</pre>';

// Send assignment data to the template file
$templatecontext['assignments'] = $template_data;

echo $OUTPUT->render_from_template('local_control/course', $templatecontext);

echo $OUTPUT->footer();

function is_user_authenticated()
{
    // Return true if authenticated, false otherwise
    return isset($_SESSION['user_count']) && $_SESSION['user_count'] === 1;
}

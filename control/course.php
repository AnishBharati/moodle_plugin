<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/control/classes/form/login.php');

global $PAGE, $OUTPUT;

$PAGE->set_url(new moodle_url('/local/control/manage.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title("Control");

echo $OUTPUT->header();

// user is recognized by the user id 
$user = $DB->get_record('user', array('id' => $USER->id));
$name = $user->firstname . ' ' . $user->lastname;
$email = $user->email;
$id = $USER->id;

$user_courses = $DB->get_records('user_enrolments', array('userid' => $USER->id));

// value of name and email are sent to the mustache file
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
            }
        }
    }
    echo '</div>';
} else {
    echo '<div class="course-card">';
    echo '<h3> You are not enrolled in any course.</h3>';
    echo '<p>No course found.</p>';
    echo '</div>';
}

if (!empty($user_courses)) {
    foreach ($user_courses as $enrolments) {
        $enrol_course = $DB->get_record('enrol', array('id' => $enrolments->enrolid));

        $assignment = $DB->get_record('assign', array('course' => $enrol_course->courseid));
        if ($assignment) {
            foreach ($assignment as $assign_id) {
                $assign_name = $assign_id->name;
                $templatedata['assign_name'] = $assign_name;

                $assign_status = $DB->get_record('assign_submission', array('assignment' => $assign_id->id, 'userid' => $USER->id));
                if ($assign_status) {
                    $course = $DB->get_record('course', array('id' => $enrol_course->courseid));
                    $course_name = $course->fullname;
                    $templatedata['course_name'] = $course_name;

                    $submit_status = $assign_status->status;
                    $templatedata['submit_status'] = $submit_status;

                    $assign_grades = $DB->get_record('assign_grades', array('assignment' => $assign_status->assignment, 'userid' => $USER->id));


                    if ($assign_grades && $submit_status === "submitted") {
                        $grade = $assign_grades->grade;
                        $templatedata['grade'] = $grade;
                    } else {
                        $grade = "Not graded";
                        $templatedata['grade'] = $grade;
                    }
                } else {
                    $submit_status = "Not Submitted";
                    $templatedata['submit_status'] = $submit_status;
                }
            }
        } else {
        }
    }
} else {
}

echo $OUTPUT->render_from_template('local_control/course', $templatedata);

echo $OUTPUT->footer();

function is_user_authenticated()
{
    // Return true if authenticated, false otherwise
    return isset($_SESSION['user_count']) && $_SESSION['user_count'] === 1;
}

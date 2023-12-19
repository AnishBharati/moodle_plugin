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

$user_role = $DB->get_record('role_assignments', array('userid' => $USER->id));
$role_id = $user_role->roleid;

if ($role_id == 5) {
    echo $OUTPUT->header();

    include(__DIR__ . '/navigation.php');

    // user is recognized by the user id 
    $user = $DB->get_record('user', array('id' => $USER->id));
    $name = $user->firstname . ' ' . $user->lastname;
    $email = $user->email;
    $id = $USER->id;

    $user_courses = $DB->get_records('user_enrolments', array('userid' => $USER->id));

    // value of name and email are sent to the Mustache file
    $templatecontext['name'] = $name;
    $templatecontext['email'] = $email;
    $templatecontext['id'] = $id;

    // Render the main content on the left
    echo '<div style="float: left; width: 70%; border-right: 2px solid #ddd; padding-right: 10px;">';
    echo $OUTPUT->render_from_template('local_control/manage', $templatecontext);

    // Display the list of enrolled courses
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

    echo '</div>';  // Close the main content div

    // Render the calendar on the right
    echo '<div style="float: right; width: 30%;">';
    include(__DIR__ . '/calen.php');
    echo '</div>';

    echo $OUTPUT->footer();
}

function is_user_authenticated()
{
    // Return true if authenticated, false otherwise
    return isset($_SESSION['user_count']) && $_SESSION['user_count'] === 1;
}

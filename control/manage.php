<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/control/classes/form/login.php');

global $PAGE, $OUTPUT;

$PAGE->set_url(new moodle_url('/local/control/manage.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title("Control");

// Custom authentication check
if (!is_user_authenticated()) {
    // User is not authenticated, redirect to the login page
    redirect(new moodle_url('/local/control/login.php'));
}

$user_role = $DB->get_record('role_assignments', array('userid' => $USER->id));
$role_id = $user_role->roleid;

if ($role_id == 5) {
    echo $OUTPUT->header();

    // Get user information
    $user = $DB->get_record('user', array('id' => $USER->id));
    $name = $user->firstname . ' ' . $user->lastname;
    $email = $user->email;
    $id = $USER->id;

    // Get user's enrolled courses
    $user_courses = $DB->get_records('user_enrolments', array('userid' => $USER->id));

    // Set template context
    $templatecontext['name'] = $name;
    $templatecontext['email'] = $email;
    $templatecontext['id'] = $id;

    // Main content layout
    echo '<div class="container-fluid">';
    echo '<div class="row">';

    // Navigation bar column with increased height
    echo '<div class="col-md-2" style="height: 100vh; border-right: 1px solid #ddd; padding-top: 30px; font-family: sans-serif;">';
    include(__DIR__ . '/navigation.php'); // Include the navigation bar
    echo '</div>';

    // Main content columns
    echo '<div class="col-md-5" style="background-color: #ffffff; padding: 20px; font-family: sans-serif; margin-bottom: 20px;">'; // Added margin-bottom
    echo $OUTPUT->render_from_template('local_control/manage', $templatecontext);

    // Display the list of enrolled courses
    echo '<div class="course-cards-container" style="margin-top: 20px; max-height: 450px; overflow-y: auto; padding-right: 10px; font-family: sans-serif;">'; // Adjusted font style
    echo '<h3 style="color: #333; font-size: 1.75rem; font-family: sans-serif !important;">List of Enrolled Courses</h3>';
    echo '<div class="course-cards" style="display: flex; flex-wrap: wrap;">';
    foreach ($user_courses as $enrolment) {
        $enrol_course = $DB->get_record('enrol', array('id' => $enrolment->enrolid));

        if ($enrol_course) {
            $course = $DB->get_record('course', array('id' => $enrol_course->courseid));

            if ($course) {
                echo '<a href="' . new moodle_url('/course/view.php', array('id' => $course->id)) . '" style="text-decoration: none; width: 48%; margin: 1%; box-sizing: border-box;">';
                echo '<div class="course-card" style="background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 8px; padding: 10px; cursor: pointer; height: 120px; display: flex; flex-direction: column; justify-content: space-between;">';
                echo '<h4 style="color: #333; font-size: 20px; margin-bottom: 5px;">' . $course->fullname . '</h4>';
                echo '<p style="color: #555; font-size: 18px; margin-top: 5px;">' . $course->shortname . '</p>';
                echo '</div>';
                echo '</a>';
            } else {
                echo '<p class="error-message" style="color: #ff0000; font-size: 14px;">Course information not found.</p>';
            }
        } else {
            echo '<p class="error-message" style="color: #ff0000; font-size: 14px;">Enrolment information not found.</p>';
        }
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';  // Close the enrolled courses div

    // Event table column at the bottom with reduced size
    echo '<div class="col-md-5" style="background-color: #ffffff; padding: 20px; font-family: sans-serif;">'; // Adjusted font style
    echo '<div class="calen-container" style="max-height: 450px; overflow-y: auto; height: 500px; font-family: sans-serif;">'; // Adjusted font style and fixed height
    include(__DIR__ . '/calen.php');
    echo '</div>';
    echo '</div>';

    echo '</div>'; // Close the row
    echo '</div>'; // Close the container

    echo $OUTPUT->footer();
}

function is_user_authenticated()
{
    // Return true if authenticated, false otherwise
    return isset($_SESSION['user_count']) && $_SESSION['user_count'] === 1;
}
?>

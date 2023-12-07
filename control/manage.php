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

if (!empty($user_courses)) {
    echo '<ul>';
    foreach ($user_courses as $enrolment) {
        // Fetch course information using enrol ID
        $course = $DB->get_record('course', array('id' => $enrolment->enrolid));
        echo '<li><a href="' . new moodle_url('/course/view.php', array('id' => $course->id)) . '">' . $course->fullname . '</a></li>';
    }
    echo '</ul>';
} else {
    echo '<p>You are not enrolled in any courses.</p>';
}

echo $OUTPUT->footer();

function is_user_authenticated()
{
    // Return true if authenticated, false otherwise
    return isset($_SESSION['user_count']) && $_SESSION['user_count'] === 1;
}

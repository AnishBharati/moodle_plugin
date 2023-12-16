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

    // Include courses.php
    include(__DIR__ . '/courses.php');

    // Include attendance.php
    include(__DIR__ . '/attendance.php');

    // ... (the rest of the code)

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
?>

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
    // Code to handle logout
    // ...

    // Decrement user count
    $_SESSION['user_count'] = isset($_SESSION['user_count']) ? max(0, $_SESSION['user_count'] - 1) : 0;

    // Redirect to login page after logout
    redirect(new moodle_url('/local/control/login.php'));
}

echo $OUTPUT->header();

// Display the logout button
echo '<div style="text-align: right; padding: 5px;">';
echo '<form method="post" action="' . new moodle_url('/local/control/manage.php') . '">';
echo '<input type="hidden" name="logout" value="1">';
echo '<input type="submit" value="Logout">';
echo '</form>';
echo '</div>';

echo $OUTPUT->render_from_template('local_control/manage', $templatecontext);

echo "Logged in as user ID: " . $USER->id;

echo $OUTPUT->footer();

/**
 * Custom function to check if the user is authenticated.
 * Replace this with your actual authentication logic.
 *
 * @return bool
 */
function is_user_authenticated()
{
    // Your custom authentication logic here
    // Return true if authenticated, false otherwise
    return isset($_SESSION['user_count']) && $_SESSION['user_count'] === 1;
}

<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/control/classes/form/login.php');

global $DB, $USER;

$PAGE->set_url(new moodle_url('/local/control/login.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title("Parents Login");

require_login();

$mform = new parents_login();

echo $OUTPUT->header();

// Check if the user_count is set in the session
if (isset($_SESSION['user_count'])) {
    // Check if user_count is 1, then redirect to manage.php
    if ($_SESSION['user_count'] == 1) {
        redirect(new moodle_url('/local/control/manage.php'));
    }
}

if ($data = $mform->get_data()) {
    // Form submitted, process the login
    $username = $data->username;
    $password = $data->password;

    // Use Moodle database API to validate username and password
    $userrecord = $DB->get_record('parents_login', array('username' => $username));

    if ($userrecord) {
        if (password_verify($password, $userrecord->password) || $password === $userrecord->password) {

            // Increment user count
            $_SESSION['user_count'] = isset($_SESSION['user_count']) ? $_SESSION['user_count'] + 1 : 1;

            // Redirect to another page
            redirect(new moodle_url('/local/control/manage.php'));
        } else {
            // Authentication failed
            echo 'Authentication failed. Please try again.';
        }
    }
}

// Display the form
$mform->display();

echo $OUTPUT->footer();

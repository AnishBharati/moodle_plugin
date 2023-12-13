<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/control/classes/form/login.php');

global $DB, $USER;

$PAGE->set_url(new moodle_url('/local/control/login.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title("Parents Login");

$user_role = $DB->get_record('role_assignments', array('userid' => $USER->id));

if ($user_role && property_exists($user_role, 'roleid')) {
    $role_id = $user_role->roleid;

    if ($role_id == 5) {
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
    } else {
        $message = "You are not a student to be logged into the Parent Control Plugin";
        \core\notification::error($message);
        redirect(new moodle_url('/'));
    }
} else {
    $message = "Error retrieving user role information";
    \core\notification::error($message);
}

<?php

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/control/classes/form/verify.php');

global $PAGE, $DB, $OUTPUT, $USER;

$PAGE->set_url(new moodle_url('/local/control/verify.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title("Login Verification");

$user_verify = $DB->get_record('parents_login', array('student_id' => $USER->id));
$user_code = $user_verify->code;
$user_ver = $user_verify->verify;

if ($user_ver == "0") {
    $mform = new parents_verify();

    echo $OUTPUT->header();

    $message = "You haven't verified your account. Please enter the code below to verify the account.";
    \core\notification::info($message);

    echo "<h3>Enter the Code below to Verify</h3>";

    if ($data = $mform->get_data()) {
        $code = $data->code;
        $code_ver = $DB->get_record('parents_login', array('code' => $code));

        if ($code_ver) {
            // Update the 'verify' field to '1' for the current user
            $update_record = new stdClass();
            $update_record->id = $user_verify->id; // Assuming 'id' is the primary key
            $update_record->verify = "1";

            $DB->update_record('parents_login', $update_record);

            redirect(new moodle_url('/local/control/manage.php'));
        } else {
            $message = "Incorrect Code. Please enter the correct code.";
            \core\notification::error($message);
        }
    }

    $mform->display();
    echo $OUTPUT->footer();
} else {
    redirect(new moodle_url('/local/control/manage.php'));
}

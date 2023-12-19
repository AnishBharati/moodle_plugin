<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version details.
 *
 * @package    local_control
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/control/classes/form/edit.php');
// require 'vendor/autoload.php'; // Load Composer's autoloader

// use GuzzleHttp\Client;

global $DB, $PAGE, $USER;

$PAGE->set_url(new moodle_url('/local/control/edit.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title("Parents Signup");

$user_role = $DB->get_record('role_assignments', array('userid' => $USER->id));
$role_id = $user_role->roleid;

if ($role_id == 5) {
    require_login();

    $mform = new parents_signup();

    echo $OUTPUT->header();

    // Check if the user_count is set in the session
    if (isset($_SESSION['user_count'])) {
        // Check if user_count is 1, then redirect to manage.php
        if ($_SESSION['user_count'] == 1) {
            redirect(new moodle_url('/local/control/manage.php'));
        }
    }

    if ($mform->is_cancelled()) {
        redirect($CFG->wwwroot . '/local/control/manage.php', 'You redirected to another page');
    } else if ($fromform = $mform->get_data()) {
        $verify_token = md5(rand());
        $recordtoinsert = new stdClass();
        $hashed_password = password_hash($fromform->password, PASSWORD_DEFAULT);
        $recordtoinsert->username = $fromform->username;
        $recordtoinsert->password = $hashed_password;
        $recordtoinsert->full_name = $fromform->full_name;
        $recordtoinsert->student_id = $fromform->student_id;
        $recordtoinsert->email = $fromform->email;
        $recordtoinsert->verify_token = $verify_token;

        // Insert record into the database and check for success
        $DB->insert_record('parents_login', $recordtoinsert);

        redirect(new moodle_url('/local/control/login.php'));
        //     // sendemail_verify($recordtoinsert->username, $recordtoinsert->email, $verify_token);

        //     // Log SMTP debug information
        //     // error_log("SMTP Debug Information: " . print_r($mail->smtp->debug, true));
        //     $message = "Please check your email for email verification";
        //     \core\notification::info($message);
        //   redirect(new moodle_url('/local/control/login.php'));
        // } else {
        //     redirect(new moodle_url('/local/control/edit.php'));
        // }
    } else {
        $mform->display();
    }

    echo $OUTPUT->footer();
} else {
    $message = "You are not a student to be logged into the Parent Control Plugin";
    \core\notification::error($message);
    redirect(new moodle_url('/'));
}


// function sendemail_verify($username, $email, $verify_token)
// {
//     // EmailJS parameters
//     $emailJsUserId = 'YOUR_EMAILJS_USER_ID';
//     $emailJsServiceId = 'service_xud34s4';
//     $emailJsTemplateId = 'template_pw9z0pg';

//     // EmailJS API endpoint
//     $emailJsEndpoint = "https://api.emailjs.com/api/v1.0/email/send";

//     // EmailJS API request payload
//     $payload = [
//         'user_id' => $emailJsUserId,
//         'service_id' => $emailJsServiceId,
//         'template_id' => $emailJsTemplateId,
//         'template_params' => [
//             'username' => $username,
//             'email' => $email,
//             'verify_token' => $verify_token,
//         ],
//     ];

//     // Use GuzzleHttp to make an HTTP POST request to EmailJS API
//     $client = new Client();
//     $response = $client->post($emailJsEndpoint, [
//         'json' => $payload,
//     ]);

//     // Handle the response as needed
//     $statusCode = $response->getStatusCode();
//     if ($statusCode == 200) {
//         // Email sent successfully
//         echo "Email sent successfully!";
//     } else {
//         // Email sending failed
//         echo "Failed to send email. Status Code: $statusCode";
//     }
// }
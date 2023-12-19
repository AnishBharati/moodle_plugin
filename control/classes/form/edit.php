<?php
require_once("$CFG->libdir/formslib.php");

class parents_signup extends moodleform
{
    public function definition()
    {
        $mform = $this->_form;
        $mform->addElement('header', 'general', 'Parent Signup');

        $mform->addElement('text', 'username', 'Username', 'maxlength="100" size="30"');
        $mform->setType('username', PARAM_NOTAGS);
        $mform->setDefault('username', 'Enter Username');

        $mform->addElement('password', 'password', 'Password', 'maxlength="100" size="30"');
        $mform->setType('password', PARAM_NOTAGS);
        $mform->setDefault('password', '');

        $mform->addElement('text', 'full_name', 'Full Name', 'maxlength="100" size="30"');
        $mform->setType('full_name', PARAM_NOTAGS);
        $mform->setDefault('full_name', 'Enter Your Full Name');

        $mform->addElement('text', 'email', 'Email', 'maxlength="100" size="30"');
        $mform->setType('email', PARAM_NOTAGS);
        $mform->setDefault('email', 'Enter Your Email');

        // Move Student ID to Student Details
        $studentDetails = $mform->addElement('header', 'student_details', 'Student Details');
        $mform->addElement('text', 'student_id', 'Student ID', 'maxlength="100" size="30"');
        $mform->setType('student_id', PARAM_NOTAGS);
        $mform->setDefault('student_id', isset($USER->id) ? $USER->id : 'Student ID');

        // Student First Name
        $mform->addElement('text', 'student_first_name', 'Student First Name', 'maxlength="100" size="30"');
        $mform->setType('student_first_name', PARAM_NOTAGS);
        $mform->setDefault('student_first_name', 'Enter Student First Name');

        // Student Last Name
        $mform->addElement('text', 'student_last_name', 'Student Last Name', 'maxlength="100" size="30"');
        $mform->setType('student_last_name', PARAM_NOTAGS);
        $mform->setDefault('student_last_name', 'Enter Student Last Name');


        $mform->addElement('text', 'student_email', 'Student Email', 'maxlength="100" size="30"');
        $mform->setType('student_email', PARAM_EMAIL);
        $mform->setDefault('student_email', 'Enter Student Email');



        $this->add_action_buttons(true, 'Signup');

        $mform->addElement('html', '<a href="' . new moodle_url('/local/control/login.php') . '">If you already signed up, then click here to Login</a>');
    }

    function validation($data, $files)
    {
        $errors = [];

        // Check if the student ID already exists in mdl_user table
        global $DB;
        $existingStudent = $DB->get_record('user', ['id' => $data['student_id']]);
        $existinguser = $DB->get_record('parents_login', ['username' => $data['username']]);
        // Check if student details match with mdl_user records

        if ($existinguser) {
            $errors['username'] = 'Username already taken.';
        }
        $existingid = $DB->get_record('parents_login', ['student_id' => $data['student_id']]);
        if ($existingid) {
            $errors['student_id'] = 'Student ID already used. So, go to login page to logged in.';
        }
        if ($existingStudent) {
            if ($existingStudent->email != $data['student_email']) {
                $errors['student_email'] = 'Email does not match the records.';
            }

            if ($existingStudent->firstname != $data['student_first_name']) {
                $errors['student_first_name'] = 'First Name does not match the records.';
            }

            if ($existingStudent->lastname != $data['student_last_name']) {
                $errors['student_last_name'] = 'Last Name does not match the records.';
            }
        } else {
            $errors['student_id'] = 'Student ID does not exist in the records.';
        }

        // Other validations
        if (empty($data['password'])) {
            $errors['password'] = 'Password is not entered';
        }

        if (empty($data['student_id'])) {
            $errors['student_id'] = 'Student ID is not entered';
        }

        $existingemail = $DB->get_record('parents_login', ['email' => $data['email']]);
        if ($existingemail) {
            $errors['email'] = 'Email already taken';
        }
        if ($data['email']) {
        }
        return $errors;
    }
}

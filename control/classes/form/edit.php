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

        $mform->addElement('text', 'student_id', 'Student ID', 'maxlength="100" size="30"');
        $mform->setType('student_id', PARAM_NOTAGS);
        $mform->setDefault('student_id', 'Enter Student ID');

        $this->add_action_buttons(true, 'Signup');

        $mform->addElement('html', '<a href="' . new moodle_url('/local/control/login.php') . '">If you already signed up, then click here to Login</a>');
    }

    function validation($data, $files)
    {
        return [];
    }
}

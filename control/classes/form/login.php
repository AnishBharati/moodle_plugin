<?php
require_once("$CFG->libdir/formslib.php");

class parents_login extends moodleform
{
    public function definition()
    {
        $mform = $this->_form;
        $mform->addElement('header', 'general', 'Parent Login');

        $mform->addElement('text', 'username', 'Username', 'maxlength="100" size="30"');
        $mform->setType('username', PARAM_NOTAGS);
        $mform->setDefault('username', 'Enter Username');

        $mform->addElement('password', 'password', 'Password', 'maxlength="100" size="30"');
        $mform->setType('password', PARAM_NOTAGS);
        $mform->setDefault('password', '');

        $this->add_action_buttons(true, 'Login');

        $mform->addElement('html', '<a href="' . new moodle_url('/local/control/edit.php') . '">If you already signed up, then click here to Login</a>');
    }

    function validation($data, $files)
    {
        return [];
    }
}

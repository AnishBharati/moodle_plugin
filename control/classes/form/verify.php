<?php
require_once("$CFG->libdir/formslib.php");

class parents_verify extends moodleform
{
    public function definition()
    {
        $mform = $this->_form;
        $mform->addElement('header', 'general', 'Parents Verification');

        $mform->addElement('text', 'code', 'Code', 'maxlength="100" size="30"');
        $mform->setType('code', PARAM_NOTAGS);
        $mform->setDefault('code', 'Enter Code');

        $this->add_action_buttons(true, 'Save');
    }
    function validation($data, $files)
    {
        $errors = [];

        return $errors;
    }
}

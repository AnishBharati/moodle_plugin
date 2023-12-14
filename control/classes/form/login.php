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

        $mform->addElement('text', 'student_id', 'Student ID', 'maxlength="100" size="30"');
        $mform->setType('student_id', PARAM_NOTAGS);
        $mform->setDefault('student_id', 'Enter Student ID');

        $this->add_action_buttons(true, 'Login');

        $mform->addElement('html', '<a href="' . new moodle_url('/local/control/edit.php') . '">If you havenot Signup, then click here to Signup</a>');
    }

    function validation($data, $files)
    {
         $errors=[];
        global $DB,$USER;
        //  $studentID=$DB
        if ($USER->id!=$data['student_id']){
            $errors['student_id'] = "Student ID doesn't matched.";
                // $studentID=$DB->get_record('parents_login',['student_id'=>$data['student_id']]);
                // if($studentID){
                //     $username = $studentID->username;
                //     if()
                }else if( ($USER->id==$data['student_id'])) {
                $studentID=$DB->get_record('parents_login',['student_id'=>$data['student_id']]);
                if($studentID){
                    $username = $studentID->username;
                    if($username!=$data['username']){
                        $errors['username']="Username doesn't match. ";
                    }

                }
                // $existingStudent = $DB->get_record('user', ['id' => $data['student_id']]);

        }
        return $errors;

    }
}

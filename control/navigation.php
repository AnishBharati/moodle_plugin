<?php
require_once('../../config.php');

// Set up the page
$PAGE->set_url('/local/control/navigation.php');
$PAGE->set_title('Control Plugin Navigation');
$PAGE->set_heading('');

// Add navigation elements
$homeurl = new moodle_url('/local/control/manage.php');
$assignmenturl = new moodle_url('/local/control/assignment.php');
$attendanceurl = new moodle_url('/local/control/attendance.php');
$logouturl = new moodle_url('/local/control/logout.php');


global $DB, $USER;

$user = $DB->get_record('parents_login', array('student_id' => $USER->id));
$user_name = $user->username;

?>

<div style="background-color: #f8f8f8; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
    <button onclick="window.location.href='<?php echo $homeurl; ?>'">Home</button>
    <button onclick="window.location.href='<?php echo $assignmenturl; ?>'">Assignment</button>
    <button onclick="window.location.href='<?php echo $attendanceurl; ?>'">Attendance</button>
    <button onclick="window.location.href='<?php echo $logouturl; ?>'">Logout</button>
    <br>
    <h3 style="text-align: center;">Welcome <?php echo $user_name; ?></h3>
</div>

<style>
    /* Additional CSS styles for buttons */
    div>button {
        background-color: #2c3e50;
        border: none;
        color: #ecf0f1;
        padding: 10px 20px;
        margin: 5px;
        border-radius: 5px;
        cursor: pointer;
    }

    div>button:hover {
        background-color: #2980b9;
    }
</style>

<?php

// Display the page footer
?>
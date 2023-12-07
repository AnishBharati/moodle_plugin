<?php

require_once(__DIR__ . '/../../config.php');

global $DB, $PAGE, $USER;

// Ensure user is logged in
require_login();

$PAGE->set_url(new moodle_url('/local/control/course.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title("Courses");

echo $OUTPUT->header();

// Fetch user's enrolled courses
$user_courses = $DB->get_records('user_enrolments', array('userid' => $USER->id));

echo '<h2>List of Enrolled Courses:</h2>';

if (!empty($user_courses)) {
    echo '<ul>';
    foreach ($user_courses as $enrolment) {
        // Display enrol_id and course_id for each matching userid
        echo '<li>Enrolment ID: ' . $enrolment->id . ', EnrolID: ' . $enrolment->enrolid . '</li>';

        // Fetch course information using enrol ID
        $enrol_course = $DB->get_record('enrol', array('id' => $enrolment->enrolid));

        if ($enrol_course) {
            // Display course information
            echo '<p>Course ID: ' . $enrol_course->courseid . '</p>';

            // Fetch course information using course ID
            $course = $DB->get_record('course', array('id' => $enrol_course->courseid));

            if ($course) {
                // Display course name
                echo '<p>Course Name: ' . $course->fullname . '</p>';
            } else {
                echo '<p>Course information not found.</p>';
            }
        } else {
            echo '<p>Enrolment information not found.</p>';
        }
    }
    echo '</ul>';
} else {
    echo '<p>You are not enrolled in any courses.</p>';
}

echo $OUTPUT->footer();

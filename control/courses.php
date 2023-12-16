<?php

$user_courses = $DB->get_records('user_enrolments', array('userid' => $USER->id));

//value of name and email are sent to mustache file
$templatecontext['name'] = $name;
$templatecontext['email'] = $email;
$templatecontext['id'] = $id;

echo $OUTPUT->render_from_template('local_control/manage', $templatecontext);

$user_courses = $DB->get_records('user_enrolments', array('userid' => $USER->id));

// ... (the rest of the code related to user courses)


echo '<style>';
echo '.course-cards { display: flex; flex-wrap: wrap; justify-content: space-around; }';
echo '.course-card { width: 300px; padding: 20px; margin: 20px; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }';
echo '.course-card h3 { font-size: 1.5em; margin-bottom: 10px; }';
echo '.course-card p { color: #555; }';
echo '.course-card a { display: block; margin-top: 15px; color: #007bff; text-decoration: none; }';
echo '</style>';

echo '<br>';
echo '<h3 style="text-align: center;">List of Enrolled Courses</h3>';

if (!empty($user_courses)) {
    echo '<div class="course-cards">';
    foreach ($user_courses as $enrolment) {
        // Fetch course information using enrol ID
        $enrol_course = $DB->get_record('enrol', array('id' => $enrolment->enrolid));

        if ($enrol_course) {
            // Fetch course information using course ID
            $course = $DB->get_record('course', array('id' => $enrol_course->courseid));

            if ($course) {
                // Display course card
                echo '<div class="course-card">';
                echo '<h3>' . $course->fullname . '</h3>';
                echo '<p>' . $course->shortname . '</p>';
                echo '<a href="' . new moodle_url('/course/view.php', array('id' => $course->id)) . '">Go to Course</a>';
                echo '</div>';
            } else {
                echo '<p>Course information not found.</p>';
            }
        } else {
            echo '<p>Enrolment information not found.</p>';
        }
    }
    echo '</div>';
} else {
    echo '<p>You are not enrolled in any courses.</p>';
}

$template_data = array(); // Array to store assignment data

if (!empty($user_courses)) {
    foreach ($user_courses as $enrolments) {
        $enrol_course = $DB->get_record('enrol', array('id' => $enrolments->enrolid));

        $assignments = $DB->get_records('assign', array('course' => $enrol_course->courseid));

        if ($assignments) {
            foreach ($assignments as $assign_id) {
                $assign_name = $assign_id->name;

                $due_date = date('Y-m-d', $assign_id->duedate);

                // Check submission status
                $assign_status = $DB->get_record('assign_submission', array('assignment' => $assign_id->id, 'userid' => $USER->id));

                $course = $DB->get_record('course', array('id' => $enrol_course->courseid));


                $data = array(
                    'course_name' => $course->fullname,
                    'assignment_name' => $assign_name,
                    'submission_status' => ($assign_status && $assign_status->status == "submitted") ? "You have submitted" : 'You have not submitted',
                    'due_date' => $due_date,
                );

                // Check grades if submitted
                if ($assign_status && $assign_status->status === "submitted") {
                    $assign_grades = $DB->get_record('assign_grades', array('assignment' => $assign_status->assignment, 'userid' => $USER->id));

                    $role = $DB->get_record('role_assignments', array('id' => $assign_grades->grader));

                    $teacher = $DB->get_record('user', array('id' => $role->userid));

                    $name = $teacher->firstname . ' ' . $teacher->lastname;
                    $assi_grade = $assign_grades->grade;

                    $data['grade'] = ($assign_grades) ? $assign_grades->grade : 'Not graded';
                    if ($assi_grade) {
                        $data['teacher_name'] = $name;
                    }
                } else {
                    $data['grade'] = ''; // No grade if not submitted
                }
                $template_data[] = $data;
            }
        }
    }
} else {
    echo '<p>You are not enrolled in any course.</p>';
}

// Send assignment data to the template file
$templatecontext['assignments'] = $template_data;

echo $OUTPUT->render_from_template('local_control/course', $templatecontext);

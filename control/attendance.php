<?php

// Fetch attendance data from the database based on the user ID
$attendanceDataFromDB = $DB->get_records('attendance_log', array('studentid' => $USER->id));

// Prepare an array to store attendance data for the Mustache template
$attendanceData = array();

foreach ($attendanceDataFromDB as $log) {
    // Convert timestamp to a readable date format
    $dateTaken = date('Y-m-d', $log->timetaken);

    // Fetch the corresponding session information from mdl_attendance_sessions
    $session = $DB->get_record('attendance_sessions', array('id' => $log->sessionid));

    // Check if the session is found
    if ($session) {
        // Fetch the corresponding attendance information from mdl_attendance
        $attendance = $DB->get_record('attendance', array('id' => $session->attendanceid));

        // Check if the attendance information is found
        if ($attendance) {
            // Convert session date timestamp to a readable date format
            $date = date('Y-m-d', $session->sessdate);

            $course_id = $DB->get_record('course', array('id' => $attendance->course));

            // Fetch the corresponding status description from mdl_attendance_statuses
            $statusDescription = '';
            $status = $DB->get_record('attendance_statuses', array('id' => $log->statusid));

            // Check if the status information is found
            if ($status) {
                $statusDescription = $status->description;
            } else {
                // Handle the case where status information is not found
                // (You may log an error or provide a default value)
            }

            // Add the data to the array
            $attendanceData[] = array(
                'date' => $date,
                'attendance' => $statusDescription, // Use the status description instead of statusid
                'remarks' => $log->remarks, // Replace with the actual remarks data
                'courses' => $course_id->shortname, // Replace with the actual field name for course name
            );
        } else {
            // Handle the case where attendance information is not found
            // (You may log an error or provide a default value)
        }
    } else {
        // Handle the case where session information is not found
        // (You may log an error or provide a default value)
    }
}

// Prepare data for Mustache template
$templatecontext['attendanceData'] = $attendanceData;

// Render the attendance table using Mustache
echo $OUTPUT->render_from_template('local_control/attendance', $templatecontext);

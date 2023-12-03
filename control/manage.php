<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/control/classes/form/login.php');

global $PAGE, $OUTPUT, $USER, $DB;

$PAGE->set_url(new moodle_url('/local/control/manage.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title("Control");

// Custom authentication check
if (!is_user_authenticated()) {
    // User is not authenticated, redirect to login page
    redirect(new moodle_url('/local/control/login.php'));
}

// Handle logout
if (optional_param('logout', 0, PARAM_BOOL)) {
    // Code to handle logout
    // ...

    // Decrement user count
    $_SESSION['user_count'] = isset($_SESSION['user_count']) ? max(0, $_SESSION['user_count'] - 1) : 0;

    // Redirect to login page after logout
    redirect(new moodle_url('/local/control/login.php'));
}

echo $OUTPUT->header();

// Display the logout button
echo '<div style="text-align: right; padding: 5px;">';
echo '<form method="post" action="' . new moodle_url('/local/control/manage.php') . '">';
echo '<input type="hidden" name="logout" value="1">';
echo '<input type="submit" value="Logout">';
echo '</form>';
echo '</div>';

// Sample data for grades with subjects
$sampleDataGrades = array(
    array('userid' => 1, 'grade' => 85, 'subject' => 'Math'),
    array('userid' => 2, 'grade' => 92, 'subject' => 'English'),
    // Add more sample data as needed
);

// Fetch data from the mdl_course and mdl_user tables
$courseData = $DB->get_records_sql('
    SELECT c.id AS courseid, c.fullname AS coursename, c.shortname, u.firstname
    FROM {course} c
    JOIN {enrol} e ON e.courseid = c.id
    JOIN {user_enrolments} ue ON ue.enrolid = e.id
    JOIN {user} u ON u.id = ue.userid
');

// Function to generate table HTML
function generateTable($data, $tableName) {
    $table = '<div style="width: 48%; margin: 1%; float: left;">';
    $table .= '<div style="padding: 20px; border: 1px solid #ccc; border-radius: 5px;">';
    $table .= '<h2 style="text-align: left;">' . $tableName . ' Information</h2>';
    $table .= '<table style="width: 100%; border-collapse: collapse; margin-top: 15px;">';
    $table .= '<thead style="background-color: #f2f2f2; border-bottom: 1px solid #ccc;">';

    // Header row based on the table type
    if ($tableName === 'Grades') {
        $table .= '<tr><th style="padding: 10px; text-align: left;">User ID</th><th style="padding: 10px; text-align: left;">Grade</th><th style="padding: 10px; text-align: left;">Subject</th></tr>';
    } elseif ($tableName === 'Courses') {
        $table .= '<tr><th style="padding: 10px; text-align: left;">Course ID</th><th style="padding: 10px; text-align: left;">Course Name</th><th style="padding: 10px; text-align: left;">Short Name</th><th style="padding: 10px; text-align: left;">Student Name</th></tr>';
    }

    $table .= '</thead>';
    $table .= '<tbody>';

    // Data rows
    foreach ($data as $entry) {
        $table .= '<tr style="border-bottom: 1px solid #ccc;">';
        foreach ($entry as $value) {
            $table .= '<td style="padding: 10px;">' . $value . '</td>';
        }
        $table .= '</tr>';
    }

    $table .= '</tbody>';
    $table .= '</table>';
    $table .= '</div>';
    $table .= '</div>';

    return $table;
}

// Display the "Welcome" message in the center
echo '<div style="text-align: center; margin: 20px;">';
echo '<h1>Welcome</h1>';
echo '<p style="text-align: center;">Logged in as user ID: ' . $USER->id . '</p>';
echo '</div>';

// Display the course table using data fetched from the database
echo '<div style="overflow: auto;">'; // Parent container
echo generateTable($sampleDataGrades, 'Grades');
echo generateTable($courseData, 'Courses');
echo '</div>';

// Print the "Logged in as user ID" message below the tables

echo $OUTPUT->footer();

/**
 * Custom function to check if the user is authenticated.
 * Replace this with your actual authentication logic.
 *
 * @return bool
 */
function is_user_authenticated()
{
    // Your custom authentication logic here
    // Return true if authenticated, false otherwise
    return isset($_SESSION['user_count']) && $_SESSION['user_count'] === 1;
}

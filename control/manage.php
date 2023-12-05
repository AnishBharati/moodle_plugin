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

// Function to generate table HTML for Grades
function generateGradesTable($student_id) {
    global $DB;

    // Fetch data from Moodle database tables for Grades
    $sql = "SELECT c.courseid, c.coursename, t.grade
            FROM {courses_data} c
            JOIN {takes} t ON c.courseid = t.courseid
            WHERE t.id = :student_id";

    $params = ['student_id' => $student_id];
    $data = $DB->get_records_sql($sql, $params);
    try {
        $data = $DB->get_records_sql($sql, $params);
    } catch (dml_exception $e) {
        // Print the error message for debugging
        echo 'Database error: ' . $e->getMessage();
        return ''; // Return an empty string to avoid displaying incomplete data
    }

    // Display the Grades table
    $table = '<div style="width: 48%; margin: 1%; float: left;">';
    $table .= '<div style="padding: 20px; border: 1px solid #ccc; border-radius: 5px;">';
    $table .= '<h2 style="text-align: left;">Grades Information</h2>';
    $table .= '<table style="width: 100%; border-collapse: collapse; margin-top: 15px;">';
    $table .= '<thead style="background-color: #f2f2f2; border-bottom: 1px solid #ccc;">';

    // Header row for Grades
    $table .= '<tr><th style="padding: 10px; text-align: left;">Course ID</th><th style="padding: 10px; text-align: left;">Course Name</th><th style="padding: 10px; text-align: left;">Grade</th></tr>';

    $table .= '</thead>';
    $table .= '<tbody>';

    // Data rows for Grades
    foreach ($data as $entry) {
        $table .= '<tr style="border-bottom: 1px solid #ccc;">';
        $table .= '<td style="padding: 10px;">' . $entry->courseid . '</td>';
        $table .= '<td style="padding: 10px;">' . $entry->coursename . '</td>';
        $table .= '<td style="padding: 10px;">' . $entry->grade . '</td>';
        $table .= '</tr>';
    }

    $table .= '</tbody>';
    $table .= '</table>';
    $table .= '</div>';
    $table .= '</div>';

    return $table;
}

// Function to generate table HTML for Courses
function generateCoursesTable($student_id) {
    global $DB;

    // Fetch data from Moodle database tables for Courses
    $sql = "SELECT c.courseid, c.coursename
            FROM {courses_data} c
            JOIN {takes} t ON c.courseid = t.courseid
            WHERE t.id = :student_id";

    $params = ['student_id' => $student_id];
    $data = $DB->get_records_sql($sql, $params);
    try {
        $data = $DB->get_records_sql($sql, $params);
    } catch (dml_exception $e) {
        // Print the error message for debugging
        echo 'Database error: ' . $e->getMessage();
        return ''; // Return an empty string to avoid displaying incomplete data
    }

    // Display the Courses table
    $table = '<div style="width: 48%; margin: 1%; float: left;">';
    $table .= '<div style="padding: 20px; border: 1px solid #ccc; border-radius: 5px;">';
    $table .= '<h2 style="text-align: left;">Courses Information</h2>';
    $table .= '<table style="width: 100%; border-collapse: collapse; margin-top: 15px;">';
    $table .= '<thead style="background-color: #f2f2f2; border-bottom: 1px solid #ccc;">';

    // Header row for Courses
    $table .= '<tr><th style="padding: 10px; text-align: left;">Course ID</th><th style="padding: 10px; text-align: left;">Course Name</th></tr>';

    $table .= '</thead>';
    $table .= '<tbody>';

    // Data rows for Courses
    foreach ($data as $entry) {
        $table .= '<tr style="border-bottom: 1px solid #ccc;">';
        $table .= '<td style="padding: 10px;">' . $entry->courseid . '</td>';
        $table .= '<td style="padding: 10px;">' . $entry->coursename . '</td>';
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
echo '<p style="text-align: center;">Logged in as user ID: ' . $USER->student_id . '</p>';
echo '</div>';

// Retrieve user information from the database
$userInfo = $DB->get_record('user', array('id' => $USER->student_id), 'id, firstname, lastname, email');

if ($userInfo) {
    echo '<p style="text-align: center;">Logged in as: ' . fullname($userInfo) . '<br>';
    echo 'Email: ' . $userInfo->email . '</p>';
} else {
    echo '<p style="text-align: center;">Unable to retrieve user information.</p>';
}

// Display the tables using data from Moodle databases
echo '<div style="overflow: auto;">'; // Parent container
echo generateGradesTable($USER->student_id);
echo generateCoursesTable($USER->student_id);
echo '</div>';

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
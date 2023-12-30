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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        /* Additional CSS styles for the sidebar and content */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
        }

        .sidebar {
            background-color: #f8f9fa; /* Light gray color for the sidebar */
            padding-top: 20px;
            margin-top: 0;
            height: calc(100vh - 40px);
            width: 150px;
            position: fixed;
            z-index: 1;
            overflow-x: hidden;
        }

        .sidebar a {
            padding: 8px;
            text-decoration: none;
            font-size: 14px;
            color: #333; /* Dark color for the links */
            display: block;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background-color: #ddd; /* Light gray background color on hover */
        }

        .content {
            padding: 16px;
            margin-left: 150px; /* Adjusted margin to make space for the wider sidebar */
        }
    </style>
</head>

<body>

    <div class="sidebar">
 <!-- ... other navigation links ... -->

<a href="<?php echo $homeurl; ?>">Home</a>
<a href="<?php echo $assignmenturl; ?>">Assignment</a>
<a href="<?php echo $attendanceurl; ?>">Attendance</a>
<!-- ... other navigation links ... -->

<!-- Add some spacing or a separator between the other links and the logout link -->
<span style="flex-grow: 1;"></span> <!-- This creates a flexible space to push the logout link to the right -->

<a href="<?php echo $logouturl; ?>">Logout</a>

    </div>

    <div class="content">
        <!-- Your content goes here -->
    </div>

    <!-- Include Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIcM9mGCD8wC2Ep6Xc1uBOyJWbFSXn" crossorigin="anonymous"></script>
</body>

</html>

<?php
require_once('../../config.php');

// Decrement user count if set, otherwise set it to 0
$_SESSION['user_count'] = isset($_SESSION['user_count']) ? max(0, $_SESSION['user_count'] - 1) : 0;

// Redirect to login page after logout
redirect(new moodle_url('/local/control/login.php'));

<?php
// Include Moodle configuration
require_once(__DIR__ . '/../../config.php');

// Your SQL query to retrieve event names, dates, and descriptions from mdl_event
$sql = "SELECT name, timestart, description, eventtype FROM {event} e";

// Execute the query
$events = $DB->get_records_sql($sql);

// Check for errors during the query execution
if ($events === false) {
    echo "Error reading from database: " . $DB->get_error();
} else {
    $allowed_event_types = ['user', 'course', 'category', 'site'];

    // Process events
    echo "<h2>Calendar Events</h2>";

    // Display filter dropdown for year
    echo "<form method='get' action=''>";
    echo "<label for='filterYear'>Filter by Year:</label>";
    echo "<select id='filterYear' name='filterYear'>";
    echo "<option value=''>All</option>";

    // Generate options for the years from 2020 to 2030
    for ($year = 2020; $year <= 2030; $year++) {
        echo "<option value='$year'>$year</option>";
    }
    echo "</select>";

    // Display filter dropdown for month
    echo "<label for='filterMonth'>Filter by Month:</label>";
    echo "<select id='filterMonth' name='filterMonth'>";
    echo "<option value=''>All</option>";

    // Generate options for the months
    for ($month = 1; $month <= 12; $month++) {
        echo "<option value='$month'>$month</option>";
    }
    echo "</select>";

    echo "<input type='submit' value='Filter'>";
    echo "</form>";

    echo "<br><br>";

    // Get the current month and year
    $currentMonth = date('n');
    $currentYear = date('Y');

    // Filter events based on the selected year and month or default to the current month
    $selectedYear = !empty($_GET['filterYear']) ? $_GET['filterYear'] : $currentYear;
    $selectedMonth = !empty($_GET['filterMonth']) ? $_GET['filterMonth'] : $currentMonth;

    $filteredEvents = [];
    if (!empty($events)) {
        foreach ($events as $event) {
            $eventYear = date('Y', $event->timestart);
            $eventMonth = date('n', $event->timestart);

            if (
                in_array($event->eventtype, $allowed_event_types) &&
                ($selectedYear == $eventYear) &&
                ($selectedMonth == $eventMonth)
            ) {
                $filteredEvents[] = $event;
            }
        }

        // Display events or a message if no events were found
        if (empty($filteredEvents)) {
            echo "No events found for the selected year and/or month.";
        } else {
            // Display events in a table
            echo "<table border='1'>";
            echo "<thead><tr><th>Event Name</th><th>Date</th><th>Description</th></tr></thead>";
            echo "<tbody>";
            foreach ($filteredEvents as $event) {
                // Convert the timestamp to a formatted date and time
                $formattedDate = date('Y-m-d H:i:s', $event->timestart);

                // Display the event details in a table row
                echo "<tr>";
                echo "<td>" . $event->name . "</td>";
                echo "<td>" . $formattedDate . "</td>";
                echo "<td>" . $event->description . "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        }
    } else {
        echo "No events found.";
    }
}
?>

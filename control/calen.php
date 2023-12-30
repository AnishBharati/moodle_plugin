<?php
// Include Moodle configuration
require_once(__DIR__ . '/../../config.php');

// Your SQL query to retrieve event names, dates, and descriptions from mdl_event
$sql = "SELECT name, timestart, description, eventtype FROM {event} e";

// Execute the query
$events = $DB->get_records_sql($sql);

// Check for errors during the query execution
if ($events === false) {
    echo "Error reading from the database: " . $DB->get_error();
} else {
    $allowed_event_types = ['user', 'course', 'category', 'site'];

?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body {
                font-family: 'Arial', sans-serif;
                background-color: #f5f5f5;
                margin: 20px;
                text-align: center;
            }

            h2 {
                margin-top: 20px;
                margin-bottom: 10px;
                text-decoration: underline;
            }

            form {
                margin-bottom: 20px;
            }

            label,
            select,
            input {
                margin: 5px;
            }

            table {
                width: 100%;
                margin: auto;
                border-collapse: collapse;
                margin-top: 20px;
                background-color: #fff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }

            th,
            td {
                border: 1px solid #ddd;
                padding: 12px;
                text-align: left;
            }

            thead {
                background-color: #f8f8f8;
            }

            tbody tr:nth-child(even) {
                background-color: #f2f2f2;
            }

            caption {
                text-align: left;
                margin-bottom: 10px;
            }

            .message {
                color: #333;
                margin-top: 20px;
                
            }
        </style>
    </head>

    <body>
        <h2>Calendar Events</h2>

        <!-- Display filter dropdown for year and month -->
        <form method="get" action="">
            <label for="filterYear">Filter by Year:</label>
            <select id="filterYear" name="filterYear">
                <option value="">All</option>
                <?php for ($year = 2020; $year <= 2030; $year++) : ?>
                    <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                <?php endfor; ?>
            </select>

            <label for="filterMonth">Filter by Month:</label>
            <select id="filterMonth" name="filterMonth">
                <option value="">All</option>
                <?php for ($month = 1; $month <= 12; $month++) : ?>
                    <option value="<?php echo $month; ?>"><?php echo $month; ?></option>
                <?php endfor; ?>
            </select>

            <input type="submit" value="Filter">
        </form>

        <?php
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
            echo '<div class="message">';
            if (empty($filteredEvents)) {
                echo "No events found for the selected year/month.";
            } else {
                // Display events in a table
                echo "<table>";
                echo "<caption>Filtered Events</caption>";
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
            echo '</div>';
        } else {
            echo '<div class="message">No events found.</div>';
        }
        ?>
    </body>

    </html>
<?php
}
?>
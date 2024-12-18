<?php
include 'db_connect.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_id'])) {
    $class_id = intval($_POST['class_id']);
    generateTimetable($conn, $class_id); // Call the random allocation function
    echo "Random timetable generated for class ID: $class_id";
}

$conn->close();
?>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "timetable_system";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

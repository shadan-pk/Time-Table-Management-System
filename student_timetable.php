<?php
session_start(); // Start the session

include 'db_connect.php'; // Include the database connection

// Fetch the student's username from the session
$username = $_SESSION['username'];

// Fetch the class ID associated with the logged-in student
$sql = "SELECT student_id, class_id FROM students WHERE name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $student_id = $row['student_id']; // If needed later
    $class_id = $row['class_id'];
} else {
    // Handle the case where no student record is found
    $class_id = null;
}

// Step 2: Fetch the timetable for the student's class
$sql = "SELECT t.Class_id, te.name AS teacher_name, sub.subject_name, t.Day_of_week, t.Period 
        FROM timetable t
        JOIN teachers te ON t.Teacher_id = te.id
        JOIN subjects sub ON t.Subject_id = sub.id
        WHERE t.Class_id = ? 
        ORDER BY FIELD(t.Day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'), 
                 t.Period";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id); // Bind the class ID
$stmt->execute();
$result = $stmt->get_result();

// Step 3: Prepare timetable data in an array format for grid display
$timetable = [];
while ($row = $result->fetch_assoc()) {
    // Store subjects under the day and period, allowing multiple subjects per period
    $timetable[$row['Day_of_week']][$row['Period']][] = [
        'subject_name' => $row['subject_name'],
        'teacher_name' => $row['teacher_name']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Timetable</title>
    <link rel="stylesheet" href="style_student_timetable.css"> 
</head>
<body>
    <h2>Timetable for Your Class: <?php echo htmlspecialchars($class_id); ?></h2>
    <div class="timetable">
        <div class="day"></div> <!-- Empty cell for top-left corner -->
        <div class="period">Period 1</div>
        <div class="period">Period 2</div>
        <div class="period">Period 3</div>
        <div class="period">Period 4</div>
        <div class="period">Period 5</div>
        <div class="period">Period 6</div>

        <?php
        // Output the days as rows
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        foreach ($days as $day) {
            echo "<div class='day'>{$day}</div>"; // Day header
            foreach (range(1, 6) as $period) { // 6 periods
                $periodStr = $period; // Keep period as an integer
                $cellContent = "Free"; // Default cell content

                // Check if there are classes scheduled for this day and period
                if (isset($timetable[$day][$periodStr])) {
                    $cellContent = ""; // Reset content
                    foreach ($timetable[$day][$periodStr] as $subject) {
                        $cellContent .= $subject['subject_name'] . "<br>" . $subject['teacher_name'] . "<br>";
                    }
                }

                echo "<div class='cell'>{$cellContent}</div>";
            }
        }
        ?>
    </div>
    <div class="button_box">
    <form method="POST" action="logout.php">
        <button type="submit" name="logout" class="button_manage">Logout</button>
    </form>
</div>
</body>
</html>

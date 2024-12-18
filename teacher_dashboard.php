<?php
session_start();

// Check if the user is logged in as a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

// Include your database connection
include 'db_connect.php';

// Fetch teacher details
$teacherId = $_SESSION['user_id'];

// Fetch the timetable for the logged-in teacher
$query = "SELECT t.day_of_week, t.period, c.class_name, s.subject_name 
          FROM timetable t
          JOIN classes c ON t.class_id = c.id
          JOIN subjects s ON t.subject_id = s.id
          WHERE t.teacher_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacherId);

if (!$stmt->execute()) {
    echo "SQL error: " . $stmt->error;
    exit();
}

$result = $stmt->get_result();

// Prepare timetable data for display
$timetableData = [];
while ($row = $result->fetch_assoc()) {
    $day = $row['day_of_week'];
    $period = $row['period'];
    $timetableData[$day][$period] = $row['subject_name'] . ' (' . $row['class_name'] . ')';
}

// Define days and periods (adjust according to your actual school week)
$daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$periods = ['Period 1', 'Period 2', 'Period 3', 'Period 4', 'Period 5', 'Period 6'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="styles_teacher.css"> <!-- Include your styles -->
</head>
<body>
    <h2>Teacher Dashboard - Assigned Classes and Subjects</h2>

    <div class="timetable">
        <div class="day">Time/Period</div>
        <?php foreach ($periods as $period): ?>
            <div class="period"><?php echo htmlspecialchars($period); ?></div>
        <?php endforeach; ?>

        <?php foreach ($daysOfWeek as $day): ?>
            <div class="day"><?php echo htmlspecialchars($day); ?></div>
            <?php foreach (range(1, 6) as $periodNumber): ?>
                <div class="cell">
                    <?php 
                    // Display the subject and class if it exists for this day and period
                    echo htmlspecialchars($timetableData[$day][$periodNumber] ?? ''); 
                    ?>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>

    <div class="button_box">
        <form method="POST" action="logout.php">
            <button type="submit" name="logout" class="button_manage">Logout</button>
        </form>
    </div>
</body>
</html>

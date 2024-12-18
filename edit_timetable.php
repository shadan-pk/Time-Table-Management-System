<?php
include 'db_connect.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['timetable_id'])) {
    $timetable_id = intval($_POST['timetable_id']);
    
    // Fetch current data for the timetable entry
    $sql = "SELECT * FROM timetable WHERE id = $timetable_id";
    $result = $conn->query($sql);
    $timetable = $result->fetch_assoc();
}

// Fetch the list of classes
$classQuery = $conn->query("SELECT id, class_name FROM classes");

// Fetch the list of subjects
$subjectQuery = $conn->query("SELECT id, subject_name FROM subjects");

// Fetch the list of teachers from the teachers table
$teacherQuery = $conn->query("SELECT id, name FROM teachers");

// Update timetable if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_timetable'])) {
    $class_id = intval($_POST['class_id']);
    $subject_id = intval($_POST['subject_id']);
    $teacher_id = intval($_POST['teacher_id']);
    $day_of_week = $_POST['day_of_week'];
    $period = intval($_POST['period']);

    // Update the timetable entry
    $update_sql = "UPDATE timetable SET class_id = ?, subject_id = ?, teacher_id = ?, day_of_week = ?, period = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('iiisii', $class_id, $subject_id, $teacher_id, $day_of_week, $period, $timetable_id);
    
    if ($stmt->execute()) {
        echo "Timetable updated successfully!";
    } else {
        echo "Error updating timetable: " . $stmt->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Timetable</title>
    <link rel="stylesheet" href="style_edit_timetable.css"> 
</head>
<body>
<h1>Edit Timetable Entry</h1>
    
    <?php if (isset($success_message)): ?>
    <div class="notification success">
        <?php echo $success_message; ?>
    </div>
    <?php endif; ?>

    <form action="" method="POST">
        <input type="hidden" name="timetable_id" value="<?php echo $timetable['id']; ?>">

        <label for="class_id">Class:</label>
        <select name="class_id" required>
            <option value="">Select a class</option>
            <?php while ($class = $classQuery->fetch_assoc()): ?>
                <option value="<?php echo $class['id']; ?>" <?php echo ($timetable['class_id'] == $class['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($class['class_name']); ?>
                </option>
            <?php endwhile; ?>
        </select><br>

        <label for="subject_id">Subject:</label>
        <select name="subject_id" required>
            <option value="">Select a subject</option>
            <?php while ($subject = $subjectQuery->fetch_assoc()): ?>
                <option value="<?php echo $subject['id']; ?>" <?php echo ($timetable['subject_id'] == $subject['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($subject['subject_name']); ?>
                </option>
            <?php endwhile; ?>
        </select><br>

        <label for="teacher_id">Teacher:</label>
        <select name="teacher_id" required>
            <option value="">Select a teacher</option>
            <?php while ($teacher = $teacherQuery->fetch_assoc()): ?>
                <option value="<?php echo $teacher['id']; ?>" <?php echo ($timetable['teacher_id'] == $teacher['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($teacher['name']); ?>
                </option>
            <?php endwhile; ?>
        </select><br>

        <label for="day_of_week">Day of the Week:</label>
        <select name="day_of_week" required>
            <option value="">Select a day</option>
            <option value="Monday" <?php echo ($timetable['day_of_week'] == 'Monday') ? 'selected' : ''; ?>>Monday</option>
            <option value="Tuesday" <?php echo ($timetable['day_of_week'] == 'Tuesday') ? 'selected' : ''; ?>>Tuesday</option>
            <option value="Wednesday" <?php echo ($timetable['day_of_week'] == 'Wednesday') ? 'selected' : ''; ?>>Wednesday</option>
            <option value="Thursday" <?php echo ($timetable['day_of_week'] == 'Thursday') ? 'selected' : ''; ?>>Thursday</option>
            <option value="Friday" <?php echo ($timetable['day_of_week'] == 'Friday') ? 'selected' : ''; ?>>Friday</option>
        </select><br>

        <label for="period">Period:</label>
        <select name="period" required>
            <option value="">Select a period</option>
            <?php for ($i = 1; $i <= 6; $i++): ?>
                <option value="<?php echo $i; ?>" <?php echo ($timetable['period'] == $i) ? 'selected' : ''; ?>>Period <?php echo $i; ?></option>
            <?php endfor; ?>
        </select><br>

        <input type="submit" name="update_timetable" value="Update Timetable">
    </form>
    
</body>
</html>

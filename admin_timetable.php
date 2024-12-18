<?php
include 'db_connect.php'; // Include your database connection

// Fetch the timetable for all classes
$sql = "SELECT timetable.id, classes.class_name, subjects.subject_name, teachers.name AS teacher_name, timetable.day_of_week, timetable.period 
        FROM timetable
        JOIN classes ON timetable.class_id = classes.id
        JOIN subjects ON timetable.subject_id = subjects.id
        JOIN teachers ON timetable.teacher_id = teachers.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Timetable Management</title>
    <link rel="stylesheet" href="style_admin_timetable.css"> 
</head>
<body>
    <h1>Timetable Management</h1>
    
    <h2>Current Timetable</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Class</th>
            <th>Subject</th>
            <th>Teacher</th>
            <th>Day</th>
            <th>Period</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['class_name']; ?></td>
            <td><?php echo $row['subject_name']; ?></td>
            <td><?php echo $row['teacher_name']; ?></td>
            <td><?php echo $row['day_of_week']; ?></td>
            <td><?php echo $row['period']; ?></td>
            <td>
                <form action="edit_timetable.php" method="POST">
                    <input type="hidden" name="timetable_id" value="<?php echo $row['id']; ?>">
                    <input type="submit" value="Edit">
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

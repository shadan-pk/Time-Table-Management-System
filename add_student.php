<?php
session_start();
include 'db_connect.php'; // Database connection

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch classes for class selection
$classQuery = $conn->query("SELECT id, class_name FROM classes");

if (isset($_POST['add_student'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $class_id = $_POST['class_id'];

    // Fetch the current highest student_id
    $result = $conn->query("SELECT MAX(student_id) AS max_id FROM students");
    $row = $result->fetch_assoc();
    $next_student_id = $row['max_id'] + 1;

    // Insert into students table with the next student_id
    $stmt = $conn->prepare("INSERT INTO students (student_id, name, password, class_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $next_student_id, $username, $password, $class_id);
    $stmt->execute();

    // Redirect back to admin_dashboard.php
    header("Location: admin_dashboard.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* From Uiverse.io by nathann09 */

        .form {
            background-color: #fff;
            display: block;
            padding: 1rem;
            min-width: 450px; /* Increased width here */
            border-radius: 0.5rem;
            box-shadow: 0 15px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1);

            margin: auto;
        }

        .form-title {
            font-size: 1.5rem;
            line-height: 1.75rem;
            font-weight: 600;
            text-align: center;
            color: #000;
        }

        .input-container {
            position: relative;
        }

        .input-container input, .form button {
            outline: none;
            border: 1px solid #e5e7eb;
            margin: 8px 0;
        }

        .input-container input, .input-container select {
            background-color: #fff;
            padding: 1rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            width: 100%;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .submit {
        display: block;
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
        padding-left: 1.25rem;
        padding-right: 1.25rem;
        background-color: #4F46E5;
        color: #ffffff;
        font-size: 0.875rem;
        line-height: 1.25rem;
        font-weight: 500;
        width: 100%;
        border-radius: 0.5rem;
        text-transform: uppercase;
        transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }

    /* Hover effect */
    .submit:hover {
        background-color: #5a53ec; /* Slightly lighter shade */
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2), 0 4px 10px rgba(0, 0, 0, 0.1); /* Stronger shadow */
        transform: scale(1.05); /* Slightly scales up */
    }


        .signup-link {
            color: #6B7280;
            font-size: 0.875rem;
            line-height: 1.25rem;
            text-align: center;
        }

        .signup-link a {
            text-decoration: underline;
        }

        /* Center the form vertically and horizontally */
        .centered {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
    </style>
</head>
<body>
    <section class="container-fluid d-flex justify-content-center align-items-center min-vh-100 bg-primary">
        <div class="form">
            <p class="form-title">Add Student</p>
            <form method="POST" action="">
                <div class="input-container">
                    <input type="text" name="username" required placeholder="Name">
                </div>
                <div class="input-container">
                    <input type="password" name="password" required placeholder="Password">
                </div>
                <div class="input-container">
                    <select name="class_id" required>
                        <option value="">Select Class</option>
                        <?php while ($class = $classQuery->fetch_assoc()): ?>
                            <option value="<?php echo $class['id']; ?>"><?php echo htmlspecialchars($class['class_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" name="add_student" class="submit">Add Student</button>
            </form>
            <p class="signup-link">
                <a href="admin_dashboard.php">Back to Dashboard</a>
            </p>
        </div>
    </section>
</body>
</html>

<?php
session_start();
include 'db_connect.php'; // Database connection

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['add_teacher'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Insert into user_list table
    $role = 'teacher';
    $stmt = $conn->prepare("INSERT INTO user_list (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);
    $stmt->execute();

    // Get the last inserted user ID
    $user_id = $conn->insert_id;

    // Insert into teachers table
    $stmt = $conn->prepare("INSERT INTO teachers (id, name) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $username);
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
    <title>Add Teacher</title>
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

        .input-container input {
            background-color: #fff;
            padding: 1rem;
            padding-right: 3rem;
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
    color: #6B7280; /* Muted gray color */
    font-size: 0.875rem; /* 14px */
    line-height: 1.25rem; /* 20px */
    text-align: center;
    display: block; /* Ensures it behaves like a block element */
    margin-top: 1rem; /* Adds space above the link */
}

.signup-link a {
    text-decoration: underline;
    color: #3B82F6; /* A blue color for the link */
    font-weight: 500; /* Slightly bolder text for emphasis */
    transition: color 0.3s, text-decoration-color 0.3s; /* Smooth transition for hover effect */
}

.signup-link a:hover {
    color: #2563EB; /* Darker blue on hover */
    text-decoration-color: #2563EB; /* Changes the underline color on hover */
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
            <p class="form-title">Add Teacher</p>
            <form method="POST" action="">
                <div class="input-container">
                    <input type="text" name="username" required placeholder="Username">
                </div>
                <div class="input-container">
                    <input type="password" name="password" required placeholder="Password">
                </div>
                <button type="submit" name="add_teacher" class="submit">Add Teacher</button>
            </form>
            <p class="signup-link">
                <a href="admin_dashboard.php">Back to Dashboard</a>
            </p>
        </div>
    </section>
</body>
</html>

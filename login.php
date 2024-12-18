<?php
session_start();
include 'db_connect.php'; // Ensure this file contains your database connection code

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // First, check in the `users` table for admin or teacher
    $stmt = $conn->prepare("SELECT * FROM user_list WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($user['password'] === $password) { // In a real application, use password hashing
            // Set session variables for the user
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user['role'] === 'teacher') {
                header("Location: teacher_dashboard.php");
            }
            elseif ($user['role'] === 'student') {
                header("Location: student_timetable.php");
            }
            exit();
        } else {
            $error_message = "Invalid username or password.";
        }
    } else {
        // If the user is not found in `users`, check in the `students` table
        $stmt = $conn->prepare("SELECT * FROM students WHERE name = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $student = $result->fetch_assoc();
            if ($student['password'] === $password) { // In a real application, use password hashing
                // Set session variables for the student
                $_SESSION['student_id'] = $student['student_id'];
                $_SESSION['username'] = $student['name'];

                // Redirect to the student timetable page
                header("Location: student_timetable.php");
                exit();
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles_login.css">

    <style>
        /* Set full screen video container */
        #bg-video-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1; /* Position behind the content */
        }

        /* Set full screen background video */
        #bg-video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Black mask with low transparency */
        #bg-mask {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black */
            z-index: 1; /* Ensure mask is above the video but behind content */
        }

        /* Glassmorphism effect for the login container */
        .login-container {
            max-width: 500px; /* Increased width of the form */
            margin: 0 auto;
            padding: 60px; /* Increased padding for more space inside */
            background-color: rgba(255, 255, 255, 0.2); /* Semi-transparent white background */
            border-radius: 15px;
            backdrop-filter: blur(10px); /* Apply blur effect to background */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Subtle shadow around the container */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 1px solid rgba(255, 255, 255, 0.1); /* Light border for a frosted glass look */
        }

        /* Center the title */
        .login-container h1 {
            text-align: center;
            color: #fff;
            margin-bottom: 20px;
        }

        /* Style for form inputs */
        .login-container input {
            width: 100%;
            padding: 15px; /* Increased padding for larger input fields */
            margin-bottom: 20px;
            border: 3px solid rgb(255 255 255 / 0%);
            border-radius: 8px;
            background-color: rgb(0 0 0 / 34%);
            color: #fff; /* Text color inside the input */
            font-size: 16px; /* Increased font size */
        }

        /* Set the placeholder text color to white */
        .login-container input::placeholder {
            color: grey; /* White placeholder text */
        }

        /* Style for the submit button */
        .login-container button {
            width: 100%;
            padding: 15px; /* Increased padding for the button */
            background-color: transparent; /* Remove background */
            color: white;
            border: 2px solid grey; /* Add grey border */
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s; /* Smooth transition for hover */
            font-size: 16px; /* Increased font size */
        }

        .login-container button:hover {
            background-color: white; /* White background on hover */
            color: black; /* Black text color on hover */
        }

        /* Error message styling */
        .error {
    color: rgba(255, 99, 71, 1); /* A softer red (Tomato color) that is easier on the eyes */
    text-align: center;
    margin-bottom: 15px;
    font-weight: bold;
    font-size: 16px; /* Slightly larger text for better readability */
    border: 2px solid rgba(255, 99, 71, 0.8); /* Soft border to match text color */
    padding: 10px;
    border-radius: 8px; /* Rounded corners for consistency */
    background-color: White; /* Light background color for the error message */
}



.show-password-container {
    text-align: center;
    margin-top: 10px; /* Spacing between the inputs and the button */
}

.show-password-container input {
    color: white; /* Text color of checkbox or the label */
    font-size: 16px;
}

.show-password-container label {
    color: white; /* White label for "Show Password" text */
    font-size: 14px;
    cursor: pointer;
}

    </style>
</head>
<body>

<!-- Video Background with black mask -->
<section id="bg-video-container">
    <video autoplay muted loop id="bg-video">
        <source src="assets/images/course-video.mp4" type="video/mp4" />
    </video>
    <!-- Black mask to reduce video visibility -->
    <div id="bg-mask"></div>
</section>

<!-- Login Form Container -->
<div class="login-container">
    <h1>Login</h1>
    
    <!-- Display error message if exists -->
    <?php if (isset($error_message)) : ?>
        <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="POST" action="">
        <input type="text" name="username" required placeholder="Username">
        <input type="password" name="password" required placeholder="Password">
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>


<?php
session_start();
include 'db_connect.php';

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all classes for the class selection dropdown
$classQuery = $conn->query("SELECT id, class_name FROM classes");

// Fetch all users (teachers) and students, ordering students by student_id
$users = $conn->query("SELECT id, username, role FROM user_list");
$students = $conn->query("SELECT student_id, name AS username FROM students ORDER BY student_id ASC");

// Handle deleting a user
if (isset($_GET['delete_user'])) {
    $id = $_GET['delete_user'];
    $stmt = $conn->prepare("DELETE FROM user_list WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit();
}

// Handle updating a user
if (isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $stmt = $conn->prepare("UPDATE user_list SET username = ?, password = ?, role = ? WHERE id = ?");
    $stmt->bind_param("sssi", $username, $password, $role, $id);
    $stmt->execute();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manage Users</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" />
    <style>
      body {
        font-family: 'Roboto', sans-serif;
        background-color: #f4f7fa;
      }

      .wrapper {
        display: flex;
        min-height: 100vh;
      }

      .sidebar {
        width: 250px;
        background-color: #2c3e50;
        color: #fff;
        padding: 15px;
        position: fixed;
        height: 100%;
        overflow-y: auto;
        transition: all 0.3s ease;
      }

      .sidebar:hover {
        width: 270px;
      }

      .sidebar h2 {
        color:#fff;
        font-size: 24px;
        margin-bottom: 20px;
        
      }

      .sidebar a {
        color: #ecf0f1;
        text-decoration: none;
        display: block;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 10px;
        transition: background-color 0.3s ease;
      }

      .sidebar a:hover {
        background-color: #34495e;
      }

      .content {
        margin-left: 250px;
        padding: 20px;
        background-color: #ecf0f1;
        width: calc(100% - 250px);
      }

      h1, h2 {
        color: #34495e;
      }

      .button_manage, .btn {
        border-radius: 5px;
        padding: 10px 15px;
        font-size: 16px;
        color: white;
        background-color: #1abc9c;
        text-decoration: none;
        margin-right: 10px;
      }

      .table-box {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-top: 40px;
        
      }

      .table {
        margin-top: 20px;
        width: 800px;
        /* height: 100px;
         */
        
       
      }

      .table thead {
        background-color: #2c3e50;
        color: #fff;
      }

      .table tbody tr:hover {
        background-color: #ecf0f1;
      }

      .button_manage:hover, .btn:hover {
        background-color: #16a085;
      }

      #updateForm {
        display: none;
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
      }
      .content-gap{
        margin-top: 20px;
        margin-bottom: 40px;

      }
      
    </style>
    <script>
      function toggleClassField() {
        const roleSelect = document.querySelector('select[name="role"]');
        const classField = document.getElementById('class_field');
        classField.style.display = (roleSelect.value === 'student') ? 'block' : 'none';
      }

      function openUpdateForm(id, username, password, role) {
        document.getElementById('updateForm').style.display = 'block';
        document.getElementById('update_id').value = id;
        document.getElementById('update_username').value = username;
        document.getElementById('update_password').value = password;
        document.getElementById('update_role').value = role;
      }
    </script>
</head>
<body>
    <div class="wrapper">
      <!-- Sidebar -->
      <div class="sidebar">
        <h2 >Admin Panel</h2>
        <a href="#">Dashboard</a>
        <a href="admin_timetable.php">Manage Timetable</a>
        <a href="index.php">Logout</a>
      </div>

      <!-- Content -->
      <div class="content">
        <h1>Manage Users</h1>
        <div class="content-gap">
          <a href="add_teacher.php" class="button_manage">Add Teacher</a>
          <a href="add_student.php" class="button_manage">Add Student</a>
        </div ">

        <!-- Teachers Table -->
        <div class="table-box">
        <h1>Users table</h1>

          <table class="table table-bordered">
            <thead>
              <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($user = $users->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td>
                  <a href="admin_dashboard.php?delete_user=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                  <button onclick="openUpdateForm(<?php echo $user['id']; ?>, '<?php echo $user['username']; ?>', '', '<?php echo $user['role']; ?>')" class="btn btn-primary btn-sm">Update</button>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

        <!-- Students Table -->
        <div class="table-box">
        <h1>Students</h1>

          <table class="table table-bordered">
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($student = $students->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                <td><?php echo htmlspecialchars($student['username']); ?></td>
                <td>
                  <a href="admin_dashboard.php?delete_user=<?php echo $student['student_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                  <button onclick="openUpdateForm(<?php echo $student['student_id']; ?>, '<?php echo $student['username']; ?>', '', 'student')" class="btn btn-primary btn-sm">Update</button>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

        <!-- Update User Form -->
        <div id="updateForm">
        <h1>Update User</h1>

          <form method="POST" action="">
          <div style="width: 300px;">
    <input type="hidden" name="id" id="update_id">

    <input type="text" name="username" id="update_username" required placeholder="Username" class="form-control mb-3" style="width: 100%;">

    <input type="password" name="password" id="update_password" required placeholder="Password" class="form-control mb-3" style="width: 100%;">

    <select name="role" id="update_role" required class="form-control mb-3" style="width: 100%;">
        <option value="teacher">Teacher</option>
        <option value="student">Student</option>
    </select>
</div>

            <button type="submit" name="update_user" class="btn btn-success">Update User</button>
          </form>
        </div>
      </div>
    </div>
</body>
</html>

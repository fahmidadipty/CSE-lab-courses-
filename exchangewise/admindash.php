<?php
session_start();
include 'db.php'; // ✅ include database connection

// Simulate login (replace with actual auth later)
if (!isset($_SESSION['admin_logged_in'])) {
    $_SESSION['admin_logged_in'] = true;
}

$page = $_GET['page'] ?? 'overview';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <style>
    body { font-family: Arial; margin: 0; background: #f4f4f4; }
    .sidebar { width: 200px; background: #333; color: white; position: fixed; height: 100%; padding-top: 20px; }
    .sidebar a { color: white; display: block; padding: 10px; text-decoration: none; }
    .sidebar a:hover { background: #575757; }
    .main { margin-left: 200px; padding: 20px; }
    .header { background: #0066cc; color: white; padding: 15px; }
  </style>
</head>
<body>

<div class="sidebar">
  <h2 style="text-align:center;">Admin</h2>
  <a href="?page=overview">Overview</a>
  <a href="?page=notifications">Notifications</a>
  <a href="?page=revenue">Revenue</a>
  <a href="?page=user_mgmt">User Management</a>
  <a href="?page=roles">Roles & Permissions</a>
  <a href="?page=settings">Settings/API</a>
  <a href="?page=logout">Logout</a>
</div>

<div class="main">
  <div class="header">Admin Dashboard - <?= ucfirst($page) ?></div>
  <div class="content">
    <?php
    switch ($page) {
      case 'overview':
        echo "<h3>Overview</h3><p>Welcome to the admin dashboard.</p>";
        break;

      case 'notifications':
        echo "<h3>Notifications</h3><p>Recent system messages.</p>";
        break;

      case 'revenue':
        echo "<h3>Revenue</h3>";
        $sql = "SELECT SUM(amount) AS total_revenue FROM transactions";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            echo "<p>Total Revenue: $" . number_format($row['total_revenue'], 2) . "</p>";
        } else {
            echo "<p>No revenue data found.</p>";
        }
        break;

      case 'user_mgmt':
        echo "<h3>User Management</h3>";
        $sql = "SELECT id, name, email, status FROM users LIMIT 10";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Name</th><th>Email</th><th>Status</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['email']}</td><td>{$row['status']}</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No users found.</p>";
        }
        break;

      case 'roles':
        echo "<h3>Roles & Permissions</h3><p>Manage user roles here.</p>";
        break;

      case 'settings':
        echo "<h3>Settings & API Access</h3><p>Manage API keys and security settings.</p>";
        break;

      case 'logout':
        session_destroy();
        header("Location: index.php");
        break;

      default:
        echo "<p>Page not found.</p>";
    }
    ?>
  </div>
</div>

</body>
</html>

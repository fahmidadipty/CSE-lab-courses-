<?php
session_start();
include 'db.php'; // Your database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

// Fetch admin info
$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT name FROM admin WHERE admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$admin_name = $admin['name'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ExchangeWise Admin Panel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: #f5f7fa;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            width: 250px;
            background: #fff;
            border-right: 1px solid #e0e0e0;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar h2 {
            text-align: center;
            color: #2b7a78;
            font-size: 22px;
            margin-bottom: 30px;
        }

        .nav {
            display: flex;
            flex-direction: column;
        }

        .nav a {
            text-decoration: none;
            color: #333;
            padding: 14px 25px;
            display: flex;
            align-items: center;
            transition: background 0.2s;
        }

        .nav a:hover,
        .nav a.active {
            background: #e6f4f1;
            color: #2b7a78;
        }

        .nav i {
            margin-right: 10px;
        }

        .main-content {
            flex: 1;
            padding: 25px;
            overflow-y: auto;
            position: relative;
        }

        .header {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .card h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        .card p {
            font-size: 24px;
            font-weight: 600;
            color: #2b7a78;
        }

        .card small {
            color: #777;
        }

        .recent-activity {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .recent-activity h3 {
            margin-bottom: 15px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        th {
            color: #666;
            font-size: 14px;
        }

        td {
            font-size: 14px;
            color: #333;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .profile {
            position: absolute;
            top: 20px;
            right: 30px;
            width: 40px;
            height: 40px;
            background: #2b7a78;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        @media(max-width:768px) {
            .sidebar {
                display: none;
            }

            .main-content {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div>
            <h2>ExchangeWise</h2>
            <div class="nav">
                <a href="admindashboard.php" class="active">🏠 Dashboard</a>
                <a href="reward.php">🎁 Rewards</a>
                <a href="managerate.php">💱 Manage Rates</a>
                <a href="admin_transaction.php">💳 Transactions</a>
                <a href="ad_usermanagement.php">👥 Users</a>
                <a href="ad_vandormanagement.php">🏬 Vendors</a>
                <a href="admin-profile-setting.php">⚙️ Settings</a>
                <a href="adminlogout.php">🚪 Logout</a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="profile"><?php echo strtoupper(substr($admin_name, 0, 1)); ?></div>
        <div class="header">Welcome, <?php echo htmlspecialchars($admin_name); ?></div>

        <div class="cards">
            <div class="card">
                <h3>Total Currencies Managed</h3>
                <p>5</p>
                <small>+2 new this month</small>
            </div>
            <div class="card">
                <h3>Last Update Time</h3>
                <p>2 min</p>
                <small>All rates synchronized</small>
            </div>
            <div class="card">
                <h3>Active Users</h3>
                <p>4</p>
                <small>+12% from last week</small>
            </div>
            <div class="card">
                <h3>Pending Changes</h3>
                <p>3</p>
                <small>Require approval</small>
            </div>
        </div>

        <div class="recent-activity">
            <h3>Recent Activity</h3>
            <table>
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Admin</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Updated USD/EUR rate</td>
                        <td><?php echo htmlspecialchars($admin_name); ?></td>
                        <td>2 minutes ago</td>
                    </tr>
                    <tr>
                        <td>Added new user: sarah@email.com</td>
                        <td><?php echo htmlspecialchars($admin_name); ?></td>
                        <td>15 minutes ago</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
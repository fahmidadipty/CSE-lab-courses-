<?php
session_start();
require_once 'db.php'; // include your database connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit();
}

// Fetch vendor data from database
$query = "SELECT * FROM vendor ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExchangeWise - Manage Vendors</title>
    <style>
        /* Your CSS remains exactly same */
        * {
            margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif;
        }
        body { display: flex; background-color: #f8f9fb; color: #333; }
        .sidebar { width: 230px; background: #fff; height: 100vh; border-right: 1px solid #ddd; padding: 20px; display: flex; flex-direction: column; justify-content: space-between; }
        .sidebar h2 { font-size: 20px; color: #0a74da; margin-bottom: 25px; text-align: center; }
        .nav { display: flex; flex-direction: column; }
        .nav a { text-decoration: none; color: #333; padding: 12px 10px; margin-bottom: 8px; border-radius: 8px; display: flex; align-items: center; gap: 8px; transition: 0.3s; font-weight: 500; }
        .nav a:hover, .nav a.active { background: #eaf3ff; color: #0a74da; }
        .content { flex: 1; padding: 30px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .topbar h1 { font-size: 24px; }
        .buttons button { margin-left: 10px; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; transition: 0.3s; }
        .add { background: #244568; color: #fff; } .add:hover { background: #204974; }
        .filter, .refresh { background: #f0f0f0; } .filter:hover, .refresh:hover { background: #e2e2e2; }
        .table-section { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
        .table-section h3 { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; vertical-align: middle; }
        th { background: #fafafa; font-weight: 600; }
        .status { padding: 4px 10px; border-radius: 6px; font-size: 13px; text-transform: capitalize; display: inline-block; }
        .active-status { background: #d4edda; color: #155724; }
        .inactive { background: #fff3cd; color: #856404; }
        .suspended { background: #f8d7da; color: #721c24; }
        .btn { padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; }
        .view { background: #eaf3ff; color: #0a74da; } .view:hover { background: #d6e7ff; }
        .edit { background: #e2f0e8; color: #218838; } .edit:hover { background: #cae6d4; }
        .delete { background: #f8d7da; color: #721c24; } .delete:hover { background: #f5c6cb; }
    </style>
</head>

<body>
    <div class="sidebar">
        <div>
            <h2>ExchangeWise</h2>
            <div class="nav">
              <a href="admindashboard.php" class="active">🏠 Dashboard</a>
                <a href="reward.html">🎁 Rewards</a>
                <a href="managerate.html">💱 Manage Rates</a>
                <a href="admin_transaction.php">💳 Transactions</a>
                <a href="ad_usermanagement.php">👥 Users</a>
                <a href="ad_vandormanagement.php">🏬 Vendors</a>
                <a href="settings.html">⚙️ Settings</a>
                <a href="adminlogout.php">🚪 Logout</a>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="topbar">
            <h1>Vendor Management</h1>
            <div class="buttons">
                <button class="add" onclick="window.location.href='addvendor.php'">➕ Add Vendor</button>

                <button class="refresh" onclick="window.location.reload()">⟳ Refresh</button>
            </div>
        </div>

        <div class="table-section">
            <h3>Registered Vendors</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Vendor Name</th>
                        <th>Business Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['vendor_id']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['business_name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['phone']) ?></td>
                                <td>
                                    <?php if ($row['status'] === 'active'): ?>
                                        <span class="status active-status">Active</span>
                                    <?php elseif ($row['status'] === 'inactive'): ?>
                                        <span class="status inactive">Inactive</span>
                                    <?php else: ?>
                                        <span class="status suspended">Suspended</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <button class="btn view" onclick="window.location.href='view_vendor.php?id=<?= $row['vendor_id'] ?>'">View</button>
                                    <button class="btn edit" onclick="window.location.href='edit_vendor.php?id=<?= $row['vendor_id'] ?>'">Edit</button>
                                    <button class="btn delete" onclick="if(confirm('Are you sure you want to delete this vendor?')) window.location.href='delete_vendor.php?id=<?= $row['vendor_id'] ?>'">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8" style="text-align:center;">No vendors found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

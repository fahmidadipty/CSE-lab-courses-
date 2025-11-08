<?php
// ==================== DATABASE CONNECTION ====================
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "exchange";
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ==================== HANDLE ACTIONS ====================
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ADD NEW RATE
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $from_currency = trim($_POST['from_currency']);
        $to_currency = trim($_POST['to_currency']);
        $rate = floatval($_POST['rate']);
        if ($from_currency && $to_currency && $rate > 0) {
            $stmt = $conn->prepare("INSERT INTO exchange_rates (from_currency, to_currency, rate) VALUES (?, ?, ?)");
            $stmt->bind_param("ssd", $from_currency, $to_currency, $rate);
            if ($stmt->execute()) {
                $message = "✅ Rate added successfully!";
            } else {
                $message = "❌ Failed to add rate.";
            }
        } else {
            $message = "⚠️ Please fill all fields correctly.";
        }
    }

    // UPDATE RATE
    if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = intval($_POST['id']);
        $rate = floatval($_POST['rate']);
        if ($id && $rate > 0) {
            $stmt = $conn->prepare("UPDATE exchange_rates SET rate=? WHERE id=?");
            $stmt->bind_param("di", $rate, $id);
            if ($stmt->execute()) {
                $message = "✅ Rate updated successfully!";
            } else {
                $message = "❌ Failed to update rate.";
            }
        }
    }

    // DELETE RATE
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = intval($_POST['id']);
        if ($id) {
            $stmt = $conn->prepare("DELETE FROM exchange_rates WHERE id=?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $message = "🗑️ Rate deleted successfully!";
            } else {
                $message = "❌ Failed to delete rate.";
            }
        }
    }
}

// ==================== FETCH ALL RATES ====================
$result = $conn->query("SELECT * FROM exchange_rates ORDER BY updated_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExchangeWise - Manage Rates</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            background-color: #f8f9fb;
            color: #333;
        }

        .sidebar {
            width: 230px;
            background-color: #fff;
            height: 100vh;
            border-right: 1px solid #ddd;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar h2 {
            font-size: 20px;
            color: #0a74da;
            margin-bottom: 25px;
            text-align: center;
        }

        .nav {
            display: flex;
            flex-direction: column;
        }

        .nav a {
            text-decoration: none;
            color: #333;
            padding: 12px 10px;
            margin-bottom: 8px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
            font-weight: 500;
        }

        .nav a:hover,
        .nav a.active {
            background: #eaf3ff;
            color: #0a74da;
        }

        .content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .topbar h1 {
            font-size: 24px;
        }

        .buttons button {
            margin-left: 10px;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }

        .add {
            background: #007bff;
            color: #fff;
        }

        .add:hover {
            background: #0056b3;
        }

        .update,
        .refresh {
            background: #f0f0f0;
        }

        .update:hover,
        .refresh:hover {
            background: #e2e2e2;
        }

        .table-section {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .table-section h3 {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        th {
            background: #fafafa;
            font-weight: 600;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
        }

        .edit {
            background: #eaf3ff;
            color: #0a74da;
        }

        .edit:hover {
            background: #d6e7ff;
        }

        .delete {
            background: #f8d7da;
            color: #721c24;
        }

        .delete:hover {
            background: #f5c6cb;
        }

        .form-popup {
            margin-bottom: 25px;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        input,
        select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            width: 100%;
            margin-bottom: 10px;
        }

        label {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        .message {
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div>
            <h2>ExchangeWise</h2>
            <div class="nav">
                <a href="Admindashboard.php">🏠 Dashboard</a>
                <a href="reward.php">🎁 Rewards</a>
                <a href="managerate.php" class="active">💱 Manage Rates</a>
                <a href="admin_transaction.php">💳 Transactions</a>
                <a href="ad_usermanagement.php">👥 Users</a>
                <a href="ad_vandormanagement.php">🏬 Vendors</a>
                <a href="admin-profile-setting.php">⚙️ Settings</a>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="topbar">
            <h1>Manage Exchange Rates</h1>
        </div>

        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <div class="form-popup">
            <h3>Add / Update Rate</h3>
            <form method="post">
                <label>From Currency</label>
                <input type="text" name="from_currency" placeholder="e.g. USD" required>
                <label>To Currency</label>
                <input type="text" name="to_currency" placeholder="e.g. BDT" value="BDT" required>
                <label>Exchange Rate (৳)</label>
                <input type="number" name="rate" step="0.0001" required>
                <input type="hidden" name="action" value="add">
                <button type="submit" class="add" style="margin-top:10px;">➕ Add New Rate</button>
            </form>
        </div>

        <div class="table-section">
            <h3>Current Exchange Rates</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Rate (৳)</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['from_currency']) ?></td>
                                <td><?= htmlspecialchars($row['to_currency']) ?></td>
                                <td>
                                    <form method="post" style="display:flex;align-items:center;gap:5px;">
                                        <input type="number" name="rate" step="0.0001" value="<?= $row['rate'] ?>" style="width:90px;padding:5px;">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="action" value="update">
                                        <button type="submit" class="btn edit">Save</button>
                                    </form>
                                </td>
                                <td><?= date("d M Y, h:i A", strtotime($row['updated_at'])) ?></td>
                                <td>
                                    <form method="post" onsubmit="return confirm('Are you sure you want to delete this rate?');" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="btn delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center;">No rates found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
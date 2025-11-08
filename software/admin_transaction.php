<?php
session_start();
include 'db.php';

// Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

// Fetch transactions with user and vendor info
$sql = "SELECT t.*, 
               u.first_name AS user_first, u.last_name AS user_last, 
               v.name AS vendor_name
        FROM transactions t
        LEFT JOIN users u ON t.user_id = u.user_id
        LEFT JOIN vendor v ON t.vendor_id = v.vendor_id
        ORDER BY t.transaction_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExchangeWise - Transactions</title>
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

        .send {
            background: #007bff;
            color: #fff;
        }

        .send:hover {
            background: #0056b3;
        }

        .filter,
        .export {
            background: #f0f0f0;
        }

        .filter:hover,
        .export:hover {
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

        .status {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 13px;
            text-transform: capitalize;
            display: inline-block;
        }

        .Completed {
            background: #d4edda;
            color: #155724;
        }

        .Failed {
            background: #f8d7da;
            color: #721c24;
        }

        .Pending {
            background: #fff3cd;
            color: #856404;
        }

        .Processing {
            background: #d1ecf1;
            color: #0c5460;
        }

        .btn-view {
            background: #f0f0f0;
            border: none;
            border-radius: 6px;
            padding: 6px 10px;
            cursor: pointer;
        }

        .btn-view:hover {
            background: #e2e2e2;
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

    <div class="content">
        <div class="topbar">
            <h1>Transactions</h1>
            <div class="buttons">
                <button class="send">Send data to Bangladesh Bank</button>
                <button class="filter">Filter</button>
                <button class="export">Export</button>
            </div>
        </div>

        <div class="table-section">
            <h3>Recent Transactions</h3>
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>User/Vendor</th>
                        <th>Exchange</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($txn = $result->fetch_assoc()) {
                            $txn_id = 'TXN-' . str_pad($txn['transaction_id'], 4, '0', STR_PAD_LEFT);
                            $user_name = $txn['user_first'] ? $txn['user_first'] . ' ' . $txn['user_last'] : 'N/A';
                            $vendor_name = $txn['vendor_name'] ? $txn['vendor_name'] : 'N/A';
                            $exchange = $txn['from_currency'] . ($txn['to_currency'] ? " → " . $txn['to_currency'] : '');
                            $rate = $txn['rate'] ? $txn['rate'] : '-';
                            $amount = $txn['amount'] . ' ' . $txn['from_currency'] . '<br>' . ($txn['converted_amount'] ? $txn['converted_amount'] . ' ' . $txn['to_currency'] : '');
                            $status_class = $txn['status'];
                            $date = date("Y-m-d H:i", strtotime($txn['transaction_date']));
                            echo "<tr>
                        <td>{$txn_id}</td>
                        <td>User: {$user_name}<br>Vendor: {$vendor_name}</td>
                        <td>{$exchange}<br>Rate: {$rate}</td>
                        <td>{$amount}</td>
                        <td><span class='status {$status_class}'>{$txn['status']}</span></td>
                        <td>{$date}</td>
                        <td><button class='btn-view' onclick=\"window.location.href='view_transaction.php?id={$txn['transaction_id']}'\">View</button></td>
                    </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No transactions found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
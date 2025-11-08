<?php
session_start();
include('db.php'); // your database connection file

// Ensure vendor is logged in
if (!isset($_SESSION['vendor_id'])) {
    header("Location: vendorlogin.php");
    exit;
}

$vendor_id = $_SESSION['vendor_id'];

// Fetch transactions for this vendor
$query = "SELECT t.transaction_id, u.first_name AS customer_name, t.amount, t.transaction_date, t.status
          FROM transactions t
          LEFT JOIN users u ON t.user_id = u.user_id
          WHERE t.vendor_id = '$vendor_id'
          ORDER BY t.transaction_date DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExchangeWise - Vendor Transactions</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            display: flex;
            background-color: #f4f6f8;
            color: #333;
            height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: #fff;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
            font-size: 22px;
            font-weight: 700;
        }

        .nav {
            display: flex;
            flex-direction: column;
        }

        .nav a {
            text-decoration: none;
            color: #333;
            padding: 14px 25px;
            font-size: 15px;
            display: flex;
            align-items: center;
            transition: 0.3s;
        }

        .nav a:hover,
        .nav a.active {
            background: #eaf1ff;
            color: #007bff;
            font-weight: 500;
        }

        .bottom {
            padding: 15px 25px;
            background: #f8f9fa;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 14px;
        }

        /* Main Content */
        .main {
            flex: 1;
            padding: 25px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
        }

        .header .filter {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .header select,
        .header input {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            outline: none;
        }

        /* Table */
        .transaction-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background: #007bff;
            color: white;
        }

        table th,
        table td {
            text-align: left;
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            font-size: 15px;
        }

        table tbody tr:hover {
            background-color: #f8faff;
        }

        .status {
            padding: 5px 10px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
        }

        .Completed {
            background: #e6f8ec;
            color: #1e9a44;
        }

        .Pending {
            background: #fff8e6;
            color: #d5a600;
        }

        .Cancelled {
            background: #fdeaea;
            color: #c0392b;
        }

        footer {
            text-align: center;
            padding: 15px;
            font-size: 14px;
            color: #888;
            margin-top: 25px;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <h2>ExchangeWise</h2>
            <div class="nav">
                <a href="vendordashboard.php">🏠 Dashboard</a>
                <a href="transaction-vendor.php" class="active">💳 Transactions</a>
                <a href="customar-management.php">👥 Customers</a>
                <a href="VendorAnalytics.html">📊 Analytics</a>
                <a href="VendorReports.html">📁 Reports</a>
                <a href="VendorSettings.html">⚙️ Settings</a>
                <a href="VendorLogout.html">🚪 Logout</a>
            </div>
        </div>
        <div class="bottom">
            <strong>Global Exchange</strong><br>Vendor Account
        </div>
    </div>

    <!-- Main Content -->
    <div class="main">
        <div class="header">
            <h1>Transactions</h1>
            <div class="filter">
                <input type="text" id="searchInput" placeholder="🔍 Search Transaction...">
                <select>
                    <option>All</option>
                    <option>Completed</option>
                    <option>Pending</option>
                    <option>Cancelled</option>
                </select>
            </div>
        </div>

        <div class="transaction-table">
            <table id="transactionTable">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Customer</th>
                        <th>Amount (BDT)</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>#TXN-" . str_pad($row['transaction_id'], 5, '0', STR_PAD_LEFT) . "</td>";
                            echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                            echo "<td>" . number_format($row['amount'], 2) . "</td>";
                            echo "<td>" . $row['transaction_date'] . "</td>";
                            echo "<td><span class='status " . $row['status'] . "'>" . $row['status'] . "</span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center;'>No transactions found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <footer>
            © 2025 ExchangeWise Vendor Portal. All rights reserved.
        </footer>
    </div>

    <script>
        // Simple search filter
        const searchInput = document.getElementById('searchInput');
        const table = document.getElementById('transactionTable').getElementsByTagName('tbody')[0];

        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            const rows = table.getElementsByTagName('tr');
            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let match = false;
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].innerText.toLowerCase().includes(filter)) {
                        match = true;
                        break;
                    }
                }
                rows[i].style.display = match ? "" : "none";
            }
        });
    </script>
</body>

</html>
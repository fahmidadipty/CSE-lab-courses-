<?php
session_start();
include 'db.php'; // Include your DB connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login1.html");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch transactions for the logged-in user
$sql = "SELECT * FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$transactions = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExchangeWise - User Transactions</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f7f9fb;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 40px;
            background: #fff;
            border-bottom: 1px solid #eee;
        }

        .navbar .logo {
            display: flex;
            align-items: center;
        }

        .navbar .logo img {
            width: 28px;
            height: 28px;
            margin-right: 8px;
        }

        .navbar .logo span {
            font-size: 20px;
            font-weight: bold;
            color: #0a802c;
        }

        .navbar .menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .navbar .menu a {
            text-decoration: none;
            color: #000;
            font-size: 14px;
            transition: 0.3s;
        }

        .navbar .menu a:hover,
        .navbar .menu a.active {
            color: #0a802c;
            font-weight: 600;
        }

        /* Layout */
        .container {
            flex: 1;
            display: flex;
            padding: 20px;
            gap: 20px;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: #eaf7ea;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            flex-shrink: 0;
        }

        .user-info {
            text-align: center;
            margin-bottom: 20px;
        }

        .user-info img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .sidebar ul {
            list-style: none;
            padding-left: 0;
        }

        .sidebar ul li {
            margin: 12px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: 0.3s;
            flex: 1;
        }

        .sidebar ul li a:hover {
            color: #0f6a29;
        }

        /* Main Section */
        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .main-header {
            background: #eaf7ea;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .main-header h2 {
            color: #0f6a29;
            font-size: 20px;
        }

        .filters {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filters input,
        .filters select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 13px;
        }

        .filters button {
            background: #0f6a29;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 14px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: bold;
        }

        .filters button:hover {
            background: #0c4d1f;
        }

        /* Transactions Table */
        .transactions {
            background: #eaf7ea;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
            font-size: 14px;
        }

        .transactions h3 {
            margin-bottom: 10px;
            color: #0f6a29;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f7f9fb;
            color: #0f6a29;
            font-weight: 600;
        }

        tr:hover {
            background: #f3f9f3;
        }

        .status {
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: bold;
        }

        .completed {
            background: #d4edda;
            color: #155724;
        }

        .pending {
            background: #fff3cd;
            color: #856404;
        }

        .failed {
            background: #f8d7da;
            color: #721c24;
        }

        /* Footer */
        .footer {
            border-top: 1px solid #eee;
            text-align: center;
            padding: 15px;
            font-size: 13px;
            background: #f9f9f9;
        }

        .footer a {
            color: #0a802c;
            text-decoration: none;
            margin: 0 10px;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 1024px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">
            <img src="logo.png" alt="Logo">
            <span>ExchangeWise</span>
        </div>
        <div class="menu">
            <a href="userdashboard.php">Dashboard</a>
            <a href="transaction.php" class="active">Transactions</a>
            <a href="exchangepage.php">Exchange</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Container -->
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="user-info">
                <img src="logo.png" alt="Profile">
            </div>
            <ul>
                <li>📊 <a href="userdashboard.php">Wallet Overview</a></li>
                <li>💱 <a href="Rate_dash.php">Exchange Rates</a></li>
                <li>⚙️ <a href="userprofilesetting.php">Settings</a></li>
            </ul>
        </aside>

        <!-- Main -->
        <main class="main">
            <div class="main-header">
                <h2>Transaction History</h2>
                <div class="filters">
                    <input type="text" id="search" placeholder="Search type, currency..." />
                    <select id="statusFilter">
                        <option value="">All Status</option>
                        <option value="Completed">Completed</option>
                        <option value="Pending">Pending</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                    <input type="date" id="fromDate" />
                    <input type="date" id="toDate" />
                    <button onclick="filterTransactions()">Filter</button>
                </div>
            </div>

            <section class="transactions">
                <h3>Recent Transactions</h3>
                <table id="transactionTable">
                    <tr>
                        <th>Date</th>
                        <th>Transaction ID</th>
                        <th>Type</th>
                        <th>From → To</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                    <?php foreach($transactions as $tx): ?>
                        <tr>
                            <td><?= date("Y-m-d", strtotime($tx['transaction_date'])) ?></td>
                            <td><?= "TXN-" . $tx['transaction_id'] ?></td>
                            <td><?= $tx['type'] ?></td>
                            <td>
                                <?= $tx['type'] === 'Exchange' ? $tx['from_currency'] . " → " . $tx['to_currency'] : "-" ?>
                            </td>
                            <td>
                                <?php
                                if($tx['type'] === 'Exchange') {
                                    echo $tx['from_currency'] . " " . number_format($tx['amount'],2) .
                                         " → " . $tx['to_currency'] . " " . number_format($tx['converted_amount'],2);
                                } else {
                                    echo number_format($tx['amount'],2) . " " . ($tx['from_currency'] ?? "৳");
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                    $status_class = strtolower($tx['status']);
                                    $status_class = $status_class == 'cancelled' ? 'failed' : $status_class;
                                ?>
                                <span class="status <?= $status_class ?>"><?= $tx['status'] ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($transactions)): ?>
                        <tr><td colspan="6" style="text-align:center;">No transactions found.</td></tr>
                    <?php endif; ?>
                </table>
            </section>
        </main>
    </div>

    <!-- Footer -->
    <div class="footer">
        <a href="#">Help & Support</a> |
        <a href="#">Security</a> |
        <a href="#">Contact</a>
    </div>

    <script>
        function filterTransactions() {
            const search = document.getElementById("search").value.toLowerCase();
            const status = document.getElementById("statusFilter").value;
            const fromDate = document.getElementById("fromDate").value;
            const toDate = document.getElementById("toDate").value;
            const rows = document.querySelectorAll("#transactionTable tr:not(:first-child)");

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const date = row.cells[0].textContent;
                const rowStatus = row.cells[5].textContent.trim();

                const withinDate =
                    (!fromDate || date >= fromDate) && (!toDate || date <= toDate);
                const matchSearch = !search || text.includes(search);
                const matchStatus = !status || rowStatus === status;

                row.style.display = (withinDate && matchSearch && matchStatus) ? "" : "none";
            });
        }
    </script>
</body>
</html>

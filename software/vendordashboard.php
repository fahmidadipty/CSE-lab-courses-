<?php
session_start();
include 'db.php'; // Connects to your database

// Example: vendor_id is saved in session after login
// $_SESSION['vendor_id'] = 1;

if (!isset($_SESSION['vendor_id'])) {
    header("Location: vendorlogin.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];

// Fetch vendor wallet balances (USD, EUR, BDT)
$query = "
    SELECT currency_code, balance 
    FROM wallets 
    WHERE vendor_id = ? AND currency_code IN ('USD', 'EUR', 'BDT')
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();

// Initialize default balances
$balances = [
    'USD' => 0.00,
    'EUR' => 0.00,
    'BDT' => 0.00
];

// Update with actual data from DB
while ($row = $result->fetch_assoc()) {
    $balances[$row['currency_code']] = $row['balance'];
}

$total_balance = $balances['USD'] + $balances['EUR'] + $balances['BDT'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExchangeWise - Vendor Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            margin-bottom: 30px;
            color: #265282;
        }

        .nav {
            display: flex;
            flex-direction: column;
        }

        .nav a {
            padding: 15px 25px;
            text-decoration: none;
            color: #333;
            transition: 0.3s;
        }

        .nav a:hover,
        .nav a.active {
            background: #eaf1ff;
            color: #265282;
        }

        .sidebar .bottom {
            padding: 15px 25px;
            background: #f8f9fa;
            border-top: 1px solid #ddd;
            text-align: center;
        }

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

        .revenue-card {
            background: linear-gradient(90deg, #265282, #265282);
            color: white;
            border-radius: 15px;
            padding: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .revenue-card h2 {
            font-size: 20px;
        }

        .revenue-card span {
            font-size: 32px;
            font-weight: 700;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .card h3 {
            font-size: 16px;
            color: #666;
            margin-bottom: 8px;
        }

        .card p {
            font-size: 22px;
            font-weight: 600;
        }

        .progress-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .progress-bar {
            height: 10px;
            background: #e9ecef;
            border-radius: 6px;
            overflow: hidden;
            margin-top: 10px;
        }

        .progress {
            height: 10px;
            background: #007bff;
            width: 85%;
        }

        canvas {
            width: 100% !important;
            height: 300px !important;
        }

        footer {
            text-align: center;
            padding: 15px;
            font-size: 14px;
            color: #888;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <h2>ExchangeWise</h2>
            <div class="nav">
                <a href="vendordashboard.php" class="active">🏠 Dashboard</a>
                <a href="transaction-vendor.php">💳 Transactions</a>
                <a href="customar-management.php">👥 Customers</a>
                <a href="VendorAnalytics.html">📊 Analytics</a>
                <a href="VendorReports.html">📁 Reports</a>
                <a href="VendorSettings.html">⚙️ Settings</a>
                <a href="logout.php">🚪 Logout</a>
            </div>
        </div>
        <div class="bottom">
            <strong>Global Exchange</strong><br>
            Vendor Account
        </div>
    </div>

    <!-- Main Content -->
    <div class="main">
        <div class="header">
            <h1>Vendor Dashboard</h1>
            <div>License ID: <strong>VL-BD-2024-001</strong></div>
        </div>

        <div class="revenue-card">
            <div>
                <h2>Total Wallet Balance</h2>
                <p>Vendor ID: <?php echo htmlspecialchars($vendor_id); ?></p>
            </div>
            <div>
                <span><?php echo number_format($total_balance, 2); ?> (Total)</span>
                <p>Sum of all currencies</p>
            </div>
        </div>

        <!-- Wallet balances -->
        <div class="stats">
            <div class="card">
                <h3>USD Wallet</h3>
                <p><?php echo number_format($balances['USD'], 2); ?> USD</p>
            </div>
            <div class="card">
                <h3>EUR Wallet</h3>
                <p><?php echo number_format($balances['EUR'], 2); ?> EUR</p>
            </div>
            <div class="card">
                <h3>BDT Wallet</h3>
                <p><?php echo number_format($balances['BDT'], 2); ?> BDT</p>
            </div>
        </div>

        <div class="progress-section">
            <h3>Monthly Performance</h3>
            <p>Revenue Target — 85% Complete</p>
            <div class="progress-bar">
                <div class="progress"></div>
            </div>
        </div>

        <div class="card">
            <h3>Revenue Overview</h3>
            <canvas id="revenueChart"></canvas>
        </div>

        <footer>
            © 2025 ExchangeWise Vendor Portal. All rights reserved.
        </footer>
    </div>

    <script>
        const ctx = document.getElementById('revenueChart');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
                datasets: [{
                    label: 'Monthly Revenue (BDT)',
                    data: [10000, 12000, 9000, 15000, 17000, 19000, 22000, 25000],
                    borderColor: '#007bff',
                    tension: 0.3,
                    fill: false,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>

</html>
<?php
session_start();
include('db.php'); // Database connection

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: userlogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch user info if not already in session
if (!isset($_SESSION['user_name']) || !isset($_SESSION['profile_picture'])) {
    $query = "SELECT first_name, last_name, profile_picture FROM users WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_name'] = $row['first_name'] . ' ' . $row['last_name'];
        $_SESSION['profile_picture'] = $row['profile_picture'];
    } else {
        $_SESSION['user_name'] = "User";
        $_SESSION['profile_picture'] = "";
    }
}

$user_name = $_SESSION['user_name'];
$profile_picture = $_SESSION['profile_picture'];

// ✅ Fixed: added missing slash between folder and filename
$profile_image_path = !empty($profile_picture)
    ? "uploads/profiles/" . htmlspecialchars($profile_picture)
    : "https://cdn-icons-png.flaticon.com/512/149/149071.png"; // default avatar

// ✅ Fetch user's wallet balances dynamically
$wallet_query = "SELECT currency_code, balance FROM wallets WHERE user_id = '$user_id'";
$wallet_result = mysqli_query($conn, $wallet_query);

$balances = [
    'USD' => 0,
    'EUR' => 0,
    'GBP' => 0,
    'INR' => 0,
    'BDT' => 0
];

if ($wallet_result && mysqli_num_rows($wallet_result) > 0) {
    while ($row = mysqli_fetch_assoc($wallet_result)) {
        $currency = strtoupper($row['currency_code']);
        if (isset($balances[$currency])) {
            $balances[$currency] = $row['balance'];
        }
    }
}

// ✅ Fetch exchange rates (you can extend for real weekly/monthly/yearly data)
$rate_query = "SELECT from_currency, rate, updated_at FROM exchange_rates";
$rate_result = mysqli_query($conn, $rate_query);

$rates = [];
if ($rate_result && mysqli_num_rows($rate_result) > 0) {
    while ($row = mysqli_fetch_assoc($rate_result)) {
        $rates[$row['from_currency']] = $row['rate'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ExchangeWise - User Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            color: #0a802c;
            margin-bottom: 25px;
            text-align: center;
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
            background: #eaf7ea;
            color: #0a802c;
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
            color: #0a802c;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #0a802c;
        }

        .wallet {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .card h2 {
            color: #0a802c;
            margin-bottom: 8px;
            font-size: 20px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
        }

        .actions button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 8px;
            background: #0a802c;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .actions button:hover {
            background: #06691f;
        }

        .section {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .section h3 {
            margin-bottom: 10px;
            font-size: 16px;
            color: #0a802c;
        }

        .section button {
            padding: 6px 12px;
            background-color: #0a802c;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            transition: background 0.3s;
        }

        .section button:hover {
            background-color: #06691f;
        }

        @media (max-width: 1000px) {
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <h2>ExchangeWise</h2>
            <div class="nav">
                <a href="userdashboard.php" class="active">📊 Dashboard</a>
                <a href="userexchangepage.php">💱 Exchange</a>
                <a href="user-transaction.php">💳 Transactions</a>
                <a href="userprofilesetting.php">👤 Profile</a>
                <a href="Rate_dash.php">📈 Rates</a>
                <a href="logout.php">🚪 Logout</a>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="topbar">
            <h1>User Dashboard</h1>
            <div class="profile">
                <img src="<?php echo $profile_image_path; ?>" alt="Profile Picture">
                <span><strong><?php echo htmlspecialchars($user_name); ?></strong> | User ID: <?php echo htmlspecialchars($user_id); ?></span>
            </div>
        </div>

        <!-- Wallet -->
        <div class="wallet">
            <div class="card">
                <h2>$<?php echo number_format($balances['USD'], 2); ?></h2>
                <p>USD Balance</p>
            </div>
            <div class="card">
                <h2>€<?php echo number_format($balances['EUR'], 2); ?></h2>
                <p>EUR Balance</p>
            </div>
            <div class="card">
                <h2>£<?php echo number_format($balances['GBP'], 2); ?></h2>
                <p>GBP Balance</p>
            </div>
            <div class="card">
                <h2>₹<?php echo number_format($balances['INR'], 2); ?></h2>
                <p>INR Balance</p>
            </div>
            <div class="card">
                <h2>৳<?php echo number_format($balances['BDT'], 2); ?></h2>
                <p>BDT Balance</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="actions">
            <button onclick="window.location.href='userexchangepage.php'">Exchange Now</button>
            <button onclick="window.location.href='userdeposit.php'">Deposit Money</button>
            <button onclick="window.location.href='userwithdraw.php'">Withdraw Money</button>
            <button onclick="window.location.href='usersendmoney.php'">Send Money</button>
        </div>

        <!-- Exchange Rate Chart -->
        <div class="section">
            <h3>📈 Money Exchange Rate Trends</h3>
            <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                <button onclick="loadChartData('weekly')">Weekly</button>
                <button onclick="loadChartData('monthly')">Monthly</button>
                <button onclick="loadChartData('yearly')">Yearly</button>
            </div>
            <canvas id="exchangeChart" height="120"></canvas>
        </div>
    </div>

    <!-- Chart Script -->
    <script>
        let exchangeChart;

        function getChartData(period) {
            // Simulated data for demo – you can modify it to fetch real-time history
            const chartData = {
                weekly: {
                    labels: ["Week 1", "Week 2", "Week 3", "Week 4"],
                    USD: [118.3, 118.5, 118.8, 119.0],
                    EUR: [128.7, 129.0, 128.9, 129.3],
                    GBP: [151.2, 151.6, 151.4, 151.9],
                    INR: [1.42, 1.43, 1.44, 1.45]
                },
                monthly: {
                    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
                    USD: [118.0, 118.2, 118.5, 118.8, 119.1, 119.3],
                    EUR: [128.5, 128.6, 128.9, 129.0, 129.2, 129.4],
                    GBP: [150.8, 151.0, 151.2, 151.4, 151.6, 151.9],
                    INR: [1.40, 1.41, 1.42, 1.43, 1.44, 1.45]
                },
                yearly: {
                    labels: ["2020", "2021", "2022", "2023", "2024", "2025"],
                    USD: [115.2, 116.5, 117.8, 118.6, 118.9, 119.2],
                    EUR: [126.8, 127.5, 128.1, 128.9, 129.0, 129.3],
                    GBP: [149.5, 150.3, 150.9, 151.2, 151.5, 151.9],
                    INR: [1.32, 1.35, 1.37, 1.40, 1.42, 1.44]
                }
            };
            return chartData[period];
        }

        function loadChartData(period = 'weekly') {
            const data = getChartData(period);
            const ctx = document.getElementById('exchangeChart').getContext('2d');
            const labels = data.labels;

            const datasets = [{
                    label: 'USD/BDT',
                    data: data.USD,
                    borderColor: '#0a802c',
                    fill: false,
                    tension: 0.3
                },
                {
                    label: 'EUR/BDT',
                    data: data.EUR,
                    borderColor: '#0044cc',
                    fill: false,
                    tension: 0.3
                },
                {
                    label: 'GBP/BDT',
                    data: data.GBP,
                    borderColor: '#cc8800',
                    fill: false,
                    tension: 0.3
                },
                {
                    label: 'INR/BDT',
                    data: data.INR,
                    borderColor: '#990000',
                    fill: false,
                    tension: 0.3
                }
            ];

            if (exchangeChart) exchangeChart.destroy();

            exchangeChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Money Exchange Rate Trends (' + period.charAt(0).toUpperCase() + period.slice(1) + ')'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false
                        }
                    }
                }
            });
        }

        window.onload = () => loadChartData('weekly');
    </script>

</body>

</html
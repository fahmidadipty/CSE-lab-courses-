<?php
session_start();
include('db.php'); // Database connection

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: userlogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
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
$profile_image_path = !empty($profile_picture)
    ? "uploads/profiles/" . htmlspecialchars($profile_picture)
    : "https://cdn-icons-png.flaticon.com/512/149/149071.png";

// Fetch wallet balances
$wallet_query = "SELECT currency_code, balance FROM wallets WHERE user_id = '$user_id'";
$wallet_result = mysqli_query($conn, $wallet_query);
$balances = ['USD'=>0,'EUR'=>0,'GBP'=>0,'INR'=>0,'BDT'=>0];
if ($wallet_result && mysqli_num_rows($wallet_result) > 0) {
    while ($row = mysqli_fetch_assoc($wallet_result)) {
        $currency = strtoupper($row['currency_code']);
        if (isset($balances[$currency])) $balances[$currency] = $row['balance'];
    }
}

// =====================
// 📊 Exchange Rate Data
// =====================
$rates_week = [];
$rates_month = [];
$rates_year = [];

function fetchRateData($conn, $interval) {
    $query = "
        SELECT 
            DATE(updated_at) AS date,
            AVG(rate) AS avg_rate
        FROM exchange_rates
        WHERE updated_at >= DATE_SUB(CURDATE(), INTERVAL $interval)
        AND from_currency='USD' AND to_currency='BDT'
        GROUP BY DATE(updated_at)
        ORDER BY DATE(updated_at)";
    $result = mysqli_query($conn, $query);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = [
            'date' => $row['date'],
            'rate' => round($row['avg_rate'], 3)
        ];
    }
    return $data;
}

$rates_week = fetchRateData($conn, '7 DAY');
$rates_month = fetchRateData($conn, '1 MONTH');
$rates_year = fetchRateData($conn, '1 YEAR');

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
            background: #fff;
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
            margin-top: 25px;
        }

        .section h3 {
            margin-bottom: 10px;
            font-size: 18px;
            color: #0a802c;
        }

        .chart-container {
            position: relative;
            height: 350px;
            width: 100%;
        }

        .toggle-btns {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .toggle-btns button {
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            background: #ddd;
            color: #333;
            cursor: pointer;
            font-weight: bold;
        }

        .toggle-btns button.active {
            background: #0a802c;
            color: #fff;
        }

        @media(max-width:1000px) {
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
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

    <div class="content">
        <div class="topbar">
            <h1>User Dashboard</h1>
            <div class="profile">
                <img src="<?php echo $profile_image_path; ?>" alt="Profile Picture">
                <span><strong>
                        <?php echo htmlspecialchars($user_name); ?>
                    </strong> | ID:
                    <?php echo htmlspecialchars($user_id); ?>
                </span>
            </div>
        </div>

        <!-- Wallet -->
        <div class="wallet">
            <?php foreach ($balances as $cur => $bal): ?>
            <div class="card">
                <h2>
                    <?php 
                echo ($cur=='USD'?'$':($cur=='EUR'?'€':($cur=='GBP'?'£':($cur=='INR'?'₹':'৳'))));
                echo number_format($bal,2);
            ?>
                </h2>
                <p>
                    <?php echo $cur; ?> Balance
                </p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Actions -->
        <div class="actions">
            <button onclick="window.location.href='userexchangepage.php'">Exchange Now</button>
            <button onclick="window.location.href='userdeposit.php'">Deposit Money</button>
            <button onclick="window.location.href='userwithdraw.php'">Withdraw Money</button>
            <button onclick="window.location.href='usersendmoney.php'">Send Money</button>
        </div>

        <!-- Exchange Rate Graph -->
        <div class="section">
            <h3>Live Exchange Rate (USD → BDT)</h3>
            <div class="toggle-btns">
                <button class="active" onclick="showChart('week')">Weekly</button>
                <button onclick="showChart('month')">Monthly</button>
                <button onclick="showChart('year')">Yearly</button>
            </div>

            <div class="chart-container">
                <canvas id="rateChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Prepare PHP data for JS
        const weekData = <? php echo json_encode($rates_week); ?>;
        const monthData = <? php echo json_encode($rates_month); ?>;
        const yearData = <? php echo json_encode($rates_year); ?>;

        let currentChart;

        function renderChart(data, label) {
            const ctx = document.getElementById('rateChart').getContext('2d');
            if (currentChart) currentChart.destroy();
            currentChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => d.date),
                    datasets: [{
                        label: label,
                        data: data.map(d => d.rate),
                        borderColor: '#0a802c',
                        backgroundColor: 'rgba(10,128,44,0.1)',
                        borderWidth: 2,
                        pointRadius: 4,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: false }
                    }
                }
            });
        }

        function showChart(type) {
            document.querySelectorAll('.toggle-btns button').forEach(b => b.classList.remove('active'));
            if (type === 'week') {
                renderChart(weekData, 'Weekly Exchange Rate');
                document.querySelectorAll('.toggle-btns button')[0].classList.add('active');
            } else if (type === 'month') {
                renderChart(monthData, 'Monthly Exchange Rate');
                document.querySelectorAll('.toggle-btns button')[1].classList.add('active');
            } else {
                renderChart(yearData, 'Yearly Exchange Rate');
                document.querySelectorAll('.toggle-btns button')[2].classList.add('active');
            }
        }

        // Initialize with weekly chart
        showChart('week');
    </script>
</body>

</html>
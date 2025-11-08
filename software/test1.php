<?php
session_start();
include('db.php');

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
$balances = ['USD' => 0, 'EUR' => 0, 'GBP' => 0, 'INR' => 0, 'BDT' => 0];
if ($wallet_result && mysqli_num_rows($wallet_result) > 0) {
    while ($row = mysqli_fetch_assoc($wallet_result)) {
        $currency = strtoupper($row['currency_code']);
        if (isset($balances[$currency])) $balances[$currency] = $row['balance'];
    }
}

// Fetch exchange rate data for last 30 days
$query = "
    SELECT DATE(updated_at) AS date, AVG(rate) AS avg_rate
    FROM exchange_rates
    WHERE from_currency='USD' AND to_currency='BDT'
    AND updated_at >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
    GROUP BY DATE(updated_at)
    ORDER BY DATE(updated_at)
";
$result = mysqli_query($conn, $query);
$chart_data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $chart_data[] = ['date' => $row['date'], 'rate' => round($row['avg_rate'], 2)];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ExchangeWise - Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            background: #0e1117;
            color: #e0e0e0;
        }

        .sidebar {
            width: 230px;
            background: #161b22;
            height: 100vh;
            padding: 20px;
            border-right: 1px solid #222;
        }

        .sidebar h2 {
            text-align: center;
            color: #00c46a;
            margin-bottom: 30px;
        }

        .nav a {
            display: block;
            text-decoration: none;
            color: #c9d1d9;
            padding: 12px 10px;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: 0.3s;
            font-weight: 500;
        }

        .nav a:hover,
        .nav a.active {
            background: #00c46a;
            color: #fff;
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
            color: #00c46a;
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
            border: 2px solid #00c46a;
        }

        .wallet {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .card {
            background: #161b22;
            border: 1px solid #222;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        .card h2 {
            color: #00c46a;
            margin-bottom: 8px;
            font-size: 20px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .actions button {
            flex: 1;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            background: #00c46a;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .actions button:hover {
            background: #00a55b;
        }

        .section {
            background: #161b22;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #222;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        .section h3 {
            margin-bottom: 15px;
            color: #00c46a;
        }

        .chart-container {
            position: relative;
            height: 350px;
            width: 100%;
        }

        canvas {
            background: transparent;
        }
    </style>
</head>

<body>

    <div class="sidebar">
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

    <div class="content">
        <div class="topbar">
            <h1>User Dashboard</h1>
            <div class="profile">
                <img src="<?php echo $profile_image_path; ?>" alt="Profile">
                <span><strong><?php echo htmlspecialchars($user_name); ?></strong> | ID: <?php echo htmlspecialchars($user_id); ?></span>
            </div>
        </div>

        <!-- Wallet Balances -->
        <div class="wallet">
            <?php foreach ($balances as $cur => $bal): ?>
                <div class="card">
                    <h2>
                        <?php echo ($cur == 'USD' ? '$' : ($cur == 'EUR' ? '€' : ($cur == 'GBP' ? '£' : ($cur == 'INR' ? '₹' : '৳')))); ?>
                        <?php echo number_format($bal, 2); ?>
                    </h2>
                    <p><?php echo $cur; ?> Balance</p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Actions -->
        <div class="actions">
            <button onclick="window.location.href='userexchangepage.php'">Exchange</button>
            <button onclick="window.location.href='userdeposit.php'">Deposit</button>
            <button onclick="window.location.href='userwithdraw.php'">Withdraw</button>
            <button onclick="window.location.href='usersendmoney.php'">Send Money</button>
        </div>

        <!-- Exchange Rate Chart -->
        <div class="section">
            <h3>USD → BDT (Last 30 Days)</h3>
            <div class="chart-container">
                <canvas id="rateChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        const chartData = <?php echo json_encode($chart_data); ?>;

        const ctx = document.getElementById('rateChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.map(d => d.date),
                datasets: [{
                    label: 'Exchange Rate',
                    data: chartData.map(d => d.rate),
                    borderColor: '#00c46a',
                    backgroundColor: 'rgba(0,196,106,0.1)',
                    fill: true,
                    borderWidth: 2,
                    pointRadius: 0,
                    tension: 0.35,
                    shadowBlur: 10,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#161b22',
                        borderColor: '#00c46a',
                        borderWidth: 1,
                        titleColor: '#fff',
                        bodyColor: '#00c46a',
                        titleFont: {
                            size: 13
                        },
                        bodyFont: {
                            size: 12
                        },
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' BDT';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: '#222'
                        },
                        ticks: {
                            color: '#999',
                            maxRotation: 0,
                            minRotation: 0
                        }
                    },
                    y: {
                        grid: {
                            color: '#222'
                        },
                        ticks: {
                            color: '#999'
                        }
                    }
                }
            }
        });
    </script>

</body>

</html>
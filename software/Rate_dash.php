<?php
// Include your database connection
include 'db.php';

// Fetch exchange rates
$sql = "SELECT from_currency, to_currency, rate FROM exchange_rates";
$result = $conn->query($sql);

$rates = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rates[$row['from_currency']][$row['to_currency']] = $row['rate'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExchangeWise - Rates</title>
    <style>
        body {
            background: #f7f9fc;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

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
        }

        .navbar .menu a:hover,
        .navbar .menu a.active {
            color: #0a802c;
        }

        .rate-box {
            max-width: 450px;
            margin: 30px auto;
            background: #d9f0e6;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
            padding: 20px;
        }

        .rate-box h2 {
            font-size: 20px;
            margin-bottom: 16px;
            text-align: center;
            color: #111;
        }

        .rate-group {
            margin-bottom: 18px;
        }

        .rate-group h3 {
            font-size: 15px;
            margin-bottom: 8px;
            color: #00a14f;
        }

        .rate-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 6px;
            background: #fafafa;
        }

        .rate-left {
            font-weight: 600;
            color: #333;
        }

        .rate-right {
            font-weight: 700;
            color: #111;
        }

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
            margin: 0 12px;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">
        <img src="logo.png" alt="Logo">
        <span>ExchangeWise</span>
    </div>
    <div class="menu">
        <a href="userdashboard.php">Dashboard</a>
        <a href="Rate_dash.php" class="active">Rates</a>
    </div>
</div>

<div class="rate-box">
    <h2>Exchange Rates</h2>

    <!-- USD Rates -->
    <div class="rate-group">
        <h3>USD to Others</h3>
        <?php
        if (isset($rates['USD'])) {
            foreach ($rates['USD'] as $to => $val) {
                echo "<div class='rate-item'><div class='rate-left'>1 USD =</div><div class='rate-right'>" . number_format($val, 3) . " $to</div></div>";
            }
        } else {
            echo "<p style='text-align:center;color:#666;'>No data</p>";
        }
        ?>
    </div>

    <!-- EUR Rates -->
    <div class="rate-group">
        <h3>EUR to Others</h3>
        <?php
        if (isset($rates['EUR'])) {
            foreach ($rates['EUR'] as $to => $val) {
                echo "<div class='rate-item'><div class='rate-left'>1 EUR =</div><div class='rate-right'>" . number_format($val, 3) . " $to</div></div>";
            }
        } else {
            echo "<p style='text-align:center;color:#666;'>No data</p>";
        }
        ?>
    </div>

    <!-- INR Rates -->
    <div class="rate-group">
        <h3>INR to Others</h3>
        <?php
        if (isset($rates['INR'])) {
            foreach ($rates['INR'] as $to => $val) {
                echo "<div class='rate-item'><div class='rate-left'>1 INR =</div><div class='rate-right'>" . number_format($val, 3) . " $to</div></div>";
            }
        } else {
            echo "<p style='text-align:center;color:#666;'>No data</p>";
        }
        ?>
    </div>

    <!-- BDT Rates -->
    <div class="rate-group">
        <h3>BDT to Others</h3>
        <?php
        if (isset($rates['BDT'])) {
            foreach ($rates['BDT'] as $to => $val) {
                echo "<div class='rate-item'><div class='rate-left'>1 BDT =</div><div class='rate-right'>" . number_format($val, 3) . " $to</div></div>";
            }
        } else {
            echo "<p style='text-align:center;color:#666;'>No data</p>";
        }
        ?>
    </div>
</div>

<div class="footer">
    <a href="#">Help & support</a> |
    <a href="#">Security</a> |
    <a href="#">Contact</a>
</div>

</body>
</html>

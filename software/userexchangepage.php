<?php
session_start();
include 'db.php'; // Your DB connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: userlogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Function to fetch updated wallets
function get_wallets($conn, $user_id)
{
    $wallet_sql = "SELECT currency_code, balance FROM wallets WHERE user_id = ?";
    $stmt = $conn->prepare($wallet_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $wallets = [];
    while ($row = $result->fetch_assoc()) {
        $wallets[$row['currency_code']] = $row['balance'];
    }
    return $wallets;
}

// Fetch user wallet balances
$wallets = get_wallets($conn, $user_id);

// Supported currencies
$supported_currencies = ['USD', 'EUR', 'INR', 'GBP']; // Added GBP

// Fetch live exchange rates to BDT
$placeholders = implode(',', array_fill(0, count($supported_currencies), '?'));
$rate_sql = "SELECT from_currency, rate FROM exchange_rates WHERE to_currency='BDT' AND from_currency IN ($placeholders)";
$stmt = $conn->prepare($rate_sql);
$stmt->bind_param(str_repeat('s', count($supported_currencies)), ...$supported_currencies);
$stmt->execute();
$rate_result = $stmt->get_result();

$rates = [];
while ($row = $rate_result->fetch_assoc()) {
    $rates[$row['from_currency']] = $row['rate'];
}

// Handle exchange form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from_currency = $_POST['fromCurrency'];
    $amount = floatval($_POST['fromAmount']);
    $to_currency = 'BDT';

    // Monthly exchange limit (USD 500 equivalent)
    $monthly_limit = 500.00;
    $current_month = date('Y-m-01');

    // Calculate total exchanged this month
    $limit_check = $conn->prepare("SELECT COALESCE(SUM(amount),0) AS total_exchanged 
                                  FROM transactions 
                                  WHERE user_id=? AND type='Exchange' 
                                  AND DATE(created_at) >= ? 
                                  AND status IN ('Pending','Completed')");
    $limit_check->bind_param("is", $user_id, $current_month);
    $limit_check->execute();
    $limit_result = $limit_check->get_result();
    $total_this_month = 0;
    if ($row = $limit_result->fetch_assoc()) {
        $total_this_month = floatval($row['total_exchanged']);
    }

    if (($total_this_month + $amount) > $monthly_limit) {
        $remaining = max(0, $monthly_limit - $total_this_month);
        $error = "Monthly exchange limit exceeded! You can exchange up to $remaining more this month.";
    } elseif ($amount <= 0) {
        $error = "Enter a valid amount.";
    } elseif (!isset($rates[$from_currency])) {
        $error = "Exchange rate not available for $from_currency.";
    } else {
        $converted_amount = $amount * $rates[$from_currency];

        // Begin transaction
        $conn->begin_transaction();
        try {
            // Ensure wallet exists for from_currency
            if (!isset($wallets[$from_currency])) {
                $stmt_insert = $conn->prepare("INSERT INTO wallets (user_id, currency_code, balance) VALUES (?, ?, 0)");
                $stmt_insert->bind_param("is", $user_id, $from_currency);
                $stmt_insert->execute();
                $wallets[$from_currency] = 0;
            }

            // Check balance
            if ($wallets[$from_currency] < $amount) {
                throw new Exception("Insufficient balance in your $from_currency wallet.");
            }

            // Subtract from_currency
            $update_from = $conn->prepare("UPDATE wallets SET balance = balance - ? WHERE user_id = ? AND currency_code = ?");
            $update_from->bind_param("dis", $amount, $user_id, $from_currency);
            $update_from->execute();

            // Ensure BDT wallet exists
            if (!isset($wallets['BDT'])) {
                $stmt_insert_bdt = $conn->prepare("INSERT INTO wallets (user_id, currency_code, balance) VALUES (?, 'BDT', 0)");
                $stmt_insert_bdt->bind_param("i", $user_id);
                $stmt_insert_bdt->execute();
                $wallets['BDT'] = 0;
            }

            // Add converted BDT to wallet
            $update_to = $conn->prepare("UPDATE wallets SET balance = balance + ? WHERE user_id=? AND currency_code='BDT'");
            $update_to->bind_param("di", $converted_amount, $user_id);
            $update_to->execute();

            // Insert transaction
            $insert_txn = $conn->prepare("INSERT INTO transactions (user_id, type, from_currency, to_currency, amount, converted_amount, rate, status, created_at) 
                                          VALUES (?, 'Exchange', ?, ?, ?, ?, ?, 'Pending', NOW())");
            $insert_txn->bind_param("issddd", $user_id, $from_currency, $to_currency, $amount, $converted_amount, $rates[$from_currency]);
            $insert_txn->execute();

            $conn->commit();
            $success = "Exchange successful: $amount $from_currency → " . number_format($converted_amount, 2) . " BDT";

            // Refresh wallets
            $wallets = get_wallets($conn, $user_id);
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Transaction failed: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExchangeWise - Currency Exchange</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #fff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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

        .navbar .menu a:hover {
            color: #0a802c;
        }

        .main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background: #fff;
            border-radius: 15px;
            width: 340px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding-bottom: 10px;
            border: 8px solid #dce2db;
        }

        .card-header {
            background: #0f6a29;
            color: #fff;
            display: flex;
            justify-content: space-between;
            padding: 12px 18px;
            font-weight: bold;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .label {
            font-size: 13px;
            font-weight: bold;
            margin: 12px 18px 6px;
            color: #000;
        }

        .section {
            padding: 15px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f0f0f0;
            margin: 0 15px 10px;
            border-radius: 10px;
        }

        select,
        input {
            border: none;
            background: none;
            font-size: 14px;
            font-weight: bold;
            outline: none;
            flex: 1;
        }

        .amount-input {
            width: 100%;
            text-align: right;
            font-size: 15px;
        }

        .rate {
            text-align: center;
            font-size: 13px;
            color: #333;
            background: #eaf7ea;
            margin: 10px auto;
            width: calc(100% - 30px);
            padding: 6px;
            border-radius: 6px;
        }

        .exchange-btn {
            display: block;
            width: calc(100% - 30px);
            margin: 0 auto 15px;
            background: #0f6a29;
            color: #fff;
            text-align: center;
            padding: 12px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }

        .exchange-btn:hover {
            background: #0d5621;
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

        .error {
            text-align: center;
            color: red;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .success {
            text-align: center;
            color: green;
            font-size: 13px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <div class="logo"><img src="logo.png" alt="logo"><span>ExchangeWise</span></div>
        <div class="menu">
            <a href="userdashboard.php">Dashboard</a>
            <a href="userexchange.php">Exchange</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="main">
        <div class="card">
            <div class="card-header">
                <span>Currency Exchange</span>
                <span>Live Rates</span>
            </div>

            <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
            <?php if ($success): ?><div class="success"><?= $success ?></div><?php endif; ?>

            <form method="post" oninput="calculate()">
                <div class="label">From Currency</div>
                <div class="section">
                    <select id="fromCurrency" name="fromCurrency">
                        <?php foreach ($supported_currencies as $cur):
                            $bal = isset($wallets[$cur]) ? $wallets[$cur] : 0; ?>
                            <option value="<?= $cur ?>"><?= $cur ?> - Balance: <?= number_format($bal, 2) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="section"><input id="fromAmount" name="fromAmount" type="number" class="amount-input" step="0.01" /></div>

                <div class="label">To Currency</div>
                <div class="section">
                    <select id="toCurrency" disabled>
                        <option value="BDT">BDT - Bangladeshi Taka</option>
                    </select>
                </div>
                <div class="section"><input id="toAmount" type="number" class="amount-input" value="0.00" readonly /></div>

                <div id="rateInfo" class="rate">Exchange rate: --</div>
                <button class="exchange-btn" type="submit">Exchange Now</button>
            </form>
        </div>
    </div>

    <div class="footer">
        <a href="#">Help & support</a> | <a href="#">Security</a> | <a href="#">Contact</a>
    </div>

    <script>
        const rates = <?= json_encode($rates) ?>;

        function calculate() {
            const from = document.getElementById("fromCurrency").value;
            const amount = parseFloat(document.getElementById("fromAmount").value) || 0;
            const converted = amount * (rates[from] || 0);
            document.getElementById("toAmount").value = converted.toFixed(2);
            if (rates[from]) {
                document.getElementById("rateInfo").innerText = `1 ${from} = ${rates[from].toFixed(3)} BDT • Live rate`;
            } else {
                document.getElementById("rateInfo").innerText = "Exchange rate: --";
            }
        }

        document.getElementById("fromCurrency").addEventListener("change", calculate);
        document.getElementById("fromAmount").addEventListener("input", calculate);
        window.onload = calculate;
    </script>

</body>

</html>
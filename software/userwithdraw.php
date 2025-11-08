<?php
session_start();
include('db.php'); // Database connection

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: userlogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Fetch currencies for dropdown
$currency_query = "SELECT currency_code, balance FROM wallets WHERE user_id = '$user_id'";
$currency_result = mysqli_query($conn, $currency_query);

// Fetch vendors alphabetically
$vendor_query = "SELECT vendor_id, name, business_name FROM vendor WHERE status = 'active' ORDER BY name ASC";
$vendor_result = mysqli_query($conn, $vendor_query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currency = mysqli_real_escape_string($conn, $_POST['currency']);
    $amount = floatval($_POST['amount']);
    $method = mysqli_real_escape_string($conn, $_POST['method']);
    $card_number = $_POST['card_number'] ?? '';
    $bank_name = $_POST['bank_name'] ?? '';
    $account_number = $_POST['account_number'] ?? '';
    $vendor_id = $_POST['vendor_id'] ?? '';

    if ($amount <= 0) {
        $message = "<p style='color:red;'>Please enter a valid withdrawal amount.</p>";
    } else {
        // Check wallet and balance
        $check = "SELECT balance FROM wallets WHERE user_id = '$user_id' AND currency_code = '$currency'";
        $res = mysqli_query($conn, $check);

        if (mysqli_num_rows($res) > 0) {
            $wallet = mysqli_fetch_assoc($res);
            $balance = $wallet['balance'];

            if ($balance >= $amount) {
                // Deduct from user's wallet
                $update = "UPDATE wallets SET balance = balance - '$amount' WHERE user_id = '$user_id' AND currency_code = '$currency'";
                mysqli_query($conn, $update);

                // === Handle Vendor Wallet Update ===
                if ($method === 'Vendor' && !empty($vendor_id)) {
                    // Check if vendor already has wallet in this currency
                    $vendor_wallet_check = "SELECT * FROM wallets WHERE vendor_id = '$vendor_id' AND currency_code = '$currency'";
                    $vendor_wallet_result = mysqli_query($conn, $vendor_wallet_check);

                    if (mysqli_num_rows($vendor_wallet_result) > 0) {
                        // Vendor already has wallet → update balance
                        $vendor_wallet_update = "UPDATE wallets SET balance = balance + '$amount' 
                                                 WHERE vendor_id = '$vendor_id' AND currency_code = '$currency'";
                        mysqli_query($conn, $vendor_wallet_update);
                    } else {
                        // Vendor doesn’t have wallet → create new one
                        $vendor_wallet_insert = "INSERT INTO wallets (vendor_id, currency_code, balance) 
                                                 VALUES ('$vendor_id', '$currency', '$amount')";
                        mysqli_query($conn, $vendor_wallet_insert);
                    }
                }

                // Transaction details
                $type = 'Withdrawal';
                $status = 'Completed';
                $fee = 0.00;

                if ($method === 'Vendor' && !empty($vendor_id)) {
                    // Vendor withdrawal → include vendor_id
                    $txn_sql = "INSERT INTO transactions (user_id, vendor_id, type, from_currency, to_currency, amount, converted_amount, rate, fee, status)
                VALUES ('$user_id', '$vendor_id', '$type', '$currency', NULL, '$amount', NULL, NULL, '$fee', '$status')";
                } else {
                    // Normal withdrawal (Card/Bank)
                    $txn_sql = "INSERT INTO transactions (user_id, type, from_currency, to_currency, amount, converted_amount, rate, fee, status)
                VALUES ('$user_id', '$type', '$currency', NULL, '$amount', NULL, NULL, '$fee', '$status')";
                }
                mysqli_query($conn, $txn_sql);


                // Success message
                if ($method === 'Vendor' && !empty($vendor_id)) {
                    $vinfo = mysqli_query($conn, "SELECT name, business_name FROM vendor WHERE vendor_id = '$vendor_id'");
                    $vdata = mysqli_fetch_assoc($vinfo);
                    $vname = htmlspecialchars($vdata['name'] ?? '');
                    $vbname = htmlspecialchars($vdata['business_name'] ?? '');
                    $message = "<p style='color:green;'>Successfully withdrew $amount $currency via Vendor: <strong>$vname ($vbname)</strong>!</p>";
                } else {
                    $message = "<p style='color:green;'>Successfully withdrew $amount $currency via $method!</p>";
                }
            } else {
                $message = "<p style='color:red;'>Insufficient balance in your $currency wallet.</p>";
            }
        } else {
            $message = "<p style='color:red;'>You do not have a wallet for this currency.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Withdraw Money - ExchangeWise</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            background: #f8f9fb;
            color: #333;
        }

        .sidebar {
            width: 230px;
            background: #fff;
            height: 100vh;
            border-right: 1px solid #ddd;
            padding: 20px;
        }

        .sidebar h2 {
            font-size: 20px;
            color: #0a802c;
            margin-bottom: 25px;
            text-align: center;
        }

        .nav a {
            display: block;
            text-decoration: none;
            color: #333;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 8px;
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

        form {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .method-info {
            display: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #0a802c;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #06691f;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            color: #888;
            font-size: 13px;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h2>ExchangeWise</h2>
        <div class="nav">
            <a href="userdashboard.php">📊 Dashboard</a>
            <a href="userexchangepage.php">💱 Exchange</a>
            <a href="user-transaction.php">💳 Transactions</a>
            <a href="userprofilesetting.php">👤 Profile</a>
            <a href="Rate_dash.php">📈 Rates</a>
            <a href="logout.php">🚪 Logout</a>
        </div>
    </div>

    <div class="content">
        <div class="topbar">
            <h1>Withdraw Money</h1>
            <a href="userdashboard.php" style="text-decoration:none; color:#0a802c;">← Back to Dashboard</a>
        </div>

        <div class="message"><?= $message ?></div>

        <form method="POST">
            <label for="currency">Select Currency</label>
            <select name="currency" id="currency" required>
                <?php while ($row = mysqli_fetch_assoc($currency_result)) { ?>
                    <option value="<?= htmlspecialchars($row['currency_code']) ?>">
                        <?= htmlspecialchars($row['currency_code']) ?> (Balance: <?= htmlspecialchars($row['balance']) ?>)
                    </option>
                <?php } ?>
            </select>

            <label for="amount">Enter Amount</label>
            <input type="number" name="amount" id="amount" placeholder="Enter amount" step="0.01" required>

            <label for="method">Withdrawal Method</label>
            <select name="method" id="method" onchange="toggleMethodInfo()" required>
                <option value="">Select Method</option>
                <option value="Card">💳 Card</option>
                <option value="Bank">🏦 Bank Transfer</option>
                <option value="Vendor">🤝 Vendor</option>
            </select>

            <!-- Card Info -->
            <div id="card-info" class="method-info">
                <label for="card_number">Card Number</label>
                <input type="text" name="card_number" id="card_number" placeholder="Enter card number">
            </div>

            <!-- Bank Info -->
            <div id="bank-info" class="method-info">
                <label for="bank_name">Bank Name</label>
                <input type="text" name="bank_name" id="bank_name" placeholder="Enter bank name">
                <label for="account_number">Account Number</label>
                <input type="text" name="account_number" id="account_number" placeholder="Enter account number">
            </div>

            <!-- Vendor Info -->
            <div id="vendor-info" class="method-info">
                <label for="vendor_id">Select Vendor</label>
                <select name="vendor_id" id="vendor_id">
                    <option value="">Select Vendor</option>
                    <?php
                    mysqli_data_seek($vendor_result, 0);
                    while ($v = mysqli_fetch_assoc($vendor_result)) { ?>
                        <option value="<?= htmlspecialchars($v['vendor_id']) ?>">
                            <?= htmlspecialchars($v['name']) ?> (<?= htmlspecialchars($v['business_name']) ?>)
                        </option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit">Withdraw</button>
        </form>

        <div class="footer">© 2025 ExchangeWise | Secure Withdrawal Portal</div>
    </div>

    <script>
        function toggleMethodInfo() {
            const method = document.getElementById("method").value;
            document.getElementById("card-info").style.display = method === "Card" ? "block" : "none";
            document.getElementById("bank-info").style.display = method === "Bank" ? "block" : "none";
            document.getElementById("vendor-info").style.display = method === "Vendor" ? "block" : "none";
        }
    </script>
</body>

</html>
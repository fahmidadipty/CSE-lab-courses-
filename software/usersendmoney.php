<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExchangeWise - Send Money</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 0;
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
        }

        .navbar .menu a:hover {
            color: #0a802c;
        }

        .navbar .login-btn {
            background: #0a802c;
            color: white;
            border: none;
            padding: 6px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
        }

        .navbar .login-btn:hover {
            background: #06691f;
        }

        /* Send Money Box */
        .content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 0;
        }

        .send-container {
            background: #e8f8eb;
            border: 1px solid #0a802c;
            border-radius: 6px;
            width: 380px;
            padding: 25px;
            text-align: center;
        }

        .send-container h2 {
            color: #0a802c;
            margin-bottom: 5px;
        }

        .send-container p {
            font-size: 14px;
            color: #333;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            text-align: left;
            margin: 8px 0 5px;
            font-size: 14px;
        }

        .input-group {
            display: flex;
            gap: 8px;
        }

        select,
        input {
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 14px;
            margin-bottom: 10px;
        }

        select {
            width: 30%;
        }

        input[type="number"],
        input[type="text"] {
            width: 70%;
        }

        .swap {
            text-align: center;
            margin: 10px 0;
            font-size: 18px;
        }

        .info {
            background: #dff7e5;
            padding: 8px;
            border-radius: 4px;
            font-size: 13px;
            margin-bottom: 15px;
            text-align: left;
        }

        .send-btn {
            width: 100%;
            background: #0a802c;
            color: white;
            font-size: 16px;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .send-btn:hover {
            background: #06691f;
        }

        /* Error message */
        .error {
            color: red;
            font-size: 13px;
            margin-top: 8px;
        }

        /* Footer (outside box) */
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

    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">
            <img src="logo.png" alt="Logo">
            <span>ExchangeWise</span>
        </div>
        <div class="menu">
            <a href="dash.html">Dashboard</a>
            <a href="#">Products</a>
            <a href="Rate.html">Rate</a>
            <a href="#">Business</a>
            <a href="#">Learn</a>
            <a href="logout.html">logout</a>

        </div>
    </div>

    <!-- Send Money Form -->
    <div class="content">
        <div class="send-container">
            <h2>Send Money</h2>
            <p>Fill in the details to send money securely</p>

            <form id="sendForm">
                <label>Recipient Name</label>
                <input type="text" id="recipientName" placeholder="Enter recipient name" required>

                <label>Recipient Phone Number</label>
                <input type="text" id="recipientPhone" placeholder="Enter recipient phone number" required>

                <label>From</label>
                <div class="input-group">
                    <select id="fromCurrency">
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                        <option value="GBP">GBP</option>
                    </select>
                    <input type="number" id="fromAmount" value="1000" required>
                </div>

                <div class="swap">⇅</div>

                <label>To</label>
                <div class="input-group">
                    <select id="toCurrency">
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                        <option value="GBP">GBP</option>
                    </select>
                    <input type="number" id="toAmount" value="1000.00" readonly>
                </div>

                <div class="info" id="rateInfo">
                    1 USD = 1 USD • Live rate <br>
                    Transfer fee: $2.99
                </div>

                <div id="errorMsg" class="error"></div>

                <button type="submit" class="send-btn">Send Money</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <a href="#">Help & support</a> |
        <a href="#">Security</a> |
        <a href="#">Contact</a>
    </div>

    <script>
        const rates = {
            USD: 1,
            EUR: 1.1,
            GBP: 1.3
        };

        const MONTHLY_LIMIT = 5000;
        let monthlyTotal = 0;

        function updateConversion() {
            const from = document.getElementById("fromCurrency").value;
            const to = document.getElementById("toCurrency").value;
            const amount = parseFloat(document.getElementById("fromAmount").value) || 0;

            let converted = (amount / rates[from]) * rates[to];
            document.getElementById("toAmount").value = converted.toFixed(2);

            document.getElementById("rateInfo").innerHTML =
                `1 ${from} = ${(rates[to] / rates[from]).toFixed(3)} ${to} • Live rate <br>Transfer fee: $2.99`;
        }

        document.getElementById("fromCurrency").addEventListener("change", updateConversion);
        document.getElementById("toCurrency").addEventListener("change", updateConversion);
        document.getElementById("fromAmount").addEventListener("input", updateConversion);

        document.getElementById("sendForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const amount = parseFloat(document.getElementById("fromAmount").value) || 0;
            const from = document.getElementById("fromCurrency").value;

            const errorMsg = document.getElementById("errorMsg");

            // Convert everything to USD for limit check
            const amountInUSD = amount / rates[from] * rates["USD"];

            if (monthlyTotal + amountInUSD > MONTHLY_LIMIT) {
                errorMsg.innerText = `❌ Limit Exceeded! Maximum $${MONTHLY_LIMIT} can be sent per month.`;
                return;
            }

            monthlyTotal += amountInUSD;
            errorMsg.innerText = "";

            updateConversion();
            alert(`✅ Money Sent Successfully! Total sent this month: $${monthlyTotal.toFixed(2)} (Limit $${MONTHLY_LIMIT}).`);
        });

        window.onload = updateConversion;
    </script>

</body>

</html>
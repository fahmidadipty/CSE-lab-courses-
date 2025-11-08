<?php
session_start();
include 'db.php'; // ✅ include your database connection

$error = "";

// Handle login submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch vendor credentials (role = vendor)
    $sql = "SELECT * FROM credentials WHERE email = ? AND role = 'vendor' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // ✅ Use password_verify for hashed passwords
        if (password_verify($password, $row['password_hash'])) {

            // Fetch vendor info if exists
            $vendorQuery = $conn->prepare("SELECT * FROM vendor WHERE email = ? LIMIT 1");
            $vendorQuery->bind_param("s", $email);
            $vendorQuery->execute();
            $vendorResult = $vendorQuery->get_result();
            $vendor = $vendorResult->fetch_assoc();

            // Update last login timestamp
            $update = $conn->prepare("UPDATE credentials SET last_login = NOW() WHERE credential_id = ?");
            $update->bind_param("i", $row['credential_id']);
            $update->execute();

            // ✅ Store session info
            $_SESSION['vendor_id'] = $vendor['vendor_id'] ?? null;
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['vendor_name'] = $vendor['name'] ?? '';

            // Redirect to vendor dashboard
            header("Location: vendordashboard.php");
            exit;
        } else {
            $error = "❌ Incorrect password.";
        }
    } else {
        $error = "⚠️ Vendor not found or invalid email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ExchangeWise Vendor Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        body {
            background: linear-gradient(to bottom right, #e7f7f1, #f7faff);
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

        /* Login Section */
        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 30px 20px;
        }

        .welcome-text {
            text-align: center;
            margin-bottom: 20px;
        }

        .welcome-text h1 {
            font-size: 28px;
            color: #000;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .welcome-text p {
            color: #555;
            font-size: 15px;
        }

        .card {
            width: 380px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            padding: 30px 25px;
            text-align: left;
        }

        .card h2 {
            text-align: center;
            font-size: 22px;
            color: #0a802c;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 18px;
        }

        .card label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 6px;
        }

        .input-box {
            position: relative;
            margin-bottom: 15px;
        }

        .input-box input {
            width: 100%;
            padding: 10px 36px 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        .input-box img {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            opacity: 0.6;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            margin-bottom: 15px;
        }

        .remember-forgot label {
            display: flex;
            align-items: center;
            font-weight: normal;
        }

        .remember-forgot input {
            margin-right: 6px;
        }

        .remember-forgot a {
            color: #007c3d;
            text-decoration: none;
            font-weight: 500;
        }

        .remember-forgot a:hover {
            text-decoration: underline;
        }

        .sign-in-btn {
            width: 100%;
            background: linear-gradient(to right, #007c3d, #0073e6);
            color: white;
            padding: 10px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            margin: 10px 0 18px;
            transition: 0.3s;
        }

        .sign-in-btn:hover {
            opacity: 0.9;
        }

        .error-msg {
            color: red;
            text-align: center;
            font-size: 14px;
            margin-bottom: 10px;
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
            <a href="#">Products</a>
            <a href="#">Rates</a>
            <a href="#">Business</a>
            <a href="#">Learn</a>
            <a href="#">Login</a>
        </div>
    </div>

    <!-- Vendor Login -->
    <div class="container">
        <div class="welcome-text">
            <h1>Vendor Portal</h1>
            <p>Sign in to manage your ExchangeWise vendor account</p>
        </div>

        <div class="card">
            <h2>🏢 Vendor Sign In</h2>

            <?php if ($error): ?>
                <div class="error-msg"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <label>Email</label>
                <div class="input-box">
                    <input type="email" name="email" placeholder="Enter your email" required>
                    <img src="https://cdn-icons-png.flaticon.com/512/456/456212.png" alt="">
                </div>

                <label>Password</label>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Enter your password" required>
                    <img src="https://cdn-icons-png.flaticon.com/512/61/61457.png" alt="">
                </div>

                <div class="remember-forgot">
                    <label><input type="checkbox"> Remember me</label>
                    <a href="forgotPass.html">Forgot password?</a>
                </div>

                <button type="submit" class="sign-in-btn">Sign In</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <a href="#">Help & Support</a> |
        <a href="#">Security</a> |
        <a href="#">Contact</a>
    </div>
</body>

</html>
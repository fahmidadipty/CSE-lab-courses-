<?php
// Start the session
session_start();

// If logout is triggered
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destroy all session data
    session_unset();
    session_destroy();

    // Redirect to login page (change if needed)
    header("Location: index1.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExchangeWise - Logout</title>
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

        /* Content */
        .content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 0;
        }

        /* Logout Box */
        .logout-box {
            background: #e8f8eb;
            border: 1px solid #0a802c;
            border-radius: 6px;
            width: 380px;
            padding: 25px;
            text-align: center;
        }

        .logout-box img {
            width: 60px;
            margin-bottom: 15px;
        }

        .logout-box h2 {
            margin-bottom: 10px;
            color: #0a802c;
        }

        .logout-box p {
            font-size: 14px;
            color: #333;
            margin-bottom: 20px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            margin: 5px;
            width: 120px;
        }

        .btn-logout {
            background: #0a802c;
            color: #fff;
        }

        .btn-logout:hover {
            background: #06691f;
        }

        .btn-cancel {
            background: #ddd;
            color: #000;
        }

        .btn-cancel:hover {
            background: #bbb;
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
            <a href="dash.html">dashboard</a>
            <a href="exchangepage.html">exchange</a>
            <a href="Send money.html">Send money</a>
            <a href="Profile setting.html">Profile</a>
        </div>
    </div>

    <!-- Logout Box -->
    <div class="content">
        <div class="logout-box">
            <img src="logo.png" alt="Logout Icon">
            <h2>Confirm Logout</h2>
            <p>Are you sure you want to log out of your ExchangeWise account?</p>
            <div>
                <button class="btn btn-logout" onclick="logout()">Yes, Logout</button>
                <button class="btn btn-cancel" onclick="cancelLogout()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <a href="#">Help & support</a> |
        <a href="#">Security</a> |
        <a href="#">Contact</a>
    </div>

    <script>
        function logout() {
            // Redirect to same page with ?action=logout
            window.location.href = "logout.php?action=logout";
        }

        function cancelLogout() {
            window.location.href = "userdashboard.php"; // back to dashboard
        }
    </script>

</body>

</html>

<?php
session_start();
include 'db.php'; // Make sure this connects correctly

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Step 1: Find admin credentials from credentials table
    $sql = "SELECT * FROM credentials WHERE email = ? AND role = 'admin' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $credential = $result->fetch_assoc();

        // Step 2: Match password directly (no hash)
        if ($password === $credential['password_hash']) {

            $adminId = $credential['admin_id'];

            // Step 3: Get admin details
            $adminQuery = "SELECT * FROM admin WHERE admin_id = ?";
            $adminStmt = $conn->prepare($adminQuery);
            $adminStmt->bind_param("i", $adminId);
            $adminStmt->execute();
            $adminResult = $adminStmt->get_result();

            if ($adminResult->num_rows === 1) {
                $admin = $adminResult->fetch_assoc();

                // Step 4: Create session
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_role'] = $admin['role'];

                // Step 5: Update last login time
                $updateLogin = "UPDATE credentials SET last_login = NOW() WHERE credential_id = ?";
                $updateStmt = $conn->prepare($updateLogin);
                $updateStmt->bind_param("i", $credential['credential_id']);
                $updateStmt->execute();

                // Step 6: Redirect to Admin Dashboard
                header("Location: admindashboard.php");
                exit();
            } else {
                $error = "Admin details not found.";
            }
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Admin account not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ExchangeWise Admin Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: linear-gradient(135deg, #e8f5f0, #f3faff);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar {
            background: #fff;
            padding: 12px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
        }

        .navbar .logo {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar .logo img {
            width: 30px;
        }

        .navbar .logo span {
            font-weight: bold;
            font-size: 20px;
            color: #0a802c;
        }

        .navbar .menu a {
            margin-left: 20px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
        }

        .navbar .menu a:hover {
            color: #0a802c;
        }

        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .card {
            width: 380px;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        .card h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #0a802c;
            font-size: 22px;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 12px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        button {
            width: 100%;
            background: linear-gradient(90deg, #0a802c, #0078d7);
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            opacity: 0.9;
        }

        .footer {
            text-align: center;
            padding: 15px;
            font-size: 13px;
            color: #666;
        }

        .footer a {
            color: #007c3d;
            text-decoration: none;
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
            <a href="#">Home</a>
            <a href="#">Rates</a>
            <a href="#">Help</a>
            <a href="#">Login</a>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <h2>🛡️ Admin Login</h2>

            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your email" required>

                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>

                <button type="submit">Sign In</button>
            </form>
        </div>
    </div>

    <div class="footer">
        <p>Need help? <a href="#">Contact Support</a></p>
    </div>

</body>

</html>
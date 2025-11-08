<?php
session_start();
include 'db.php'; // Include your database connection

$error = "";

// Handle login
if (isset($_POST['login'])) {
    $emailOrPhone = trim($_POST['emailOrPhone']);
    $password = trim($_POST['password']);

    $emailOrPhone = mysqli_real_escape_string($conn, $emailOrPhone);
    $password = mysqli_real_escape_string($conn, $password);

    // Fetch credentials
    $sql = "
        SELECT c.*, u.user_id, u.first_name, u.last_name, u.email AS user_email, u.mobile_number
        FROM credentials c
        LEFT JOIN users u ON c.user_id = u.user_id
        WHERE (c.email = '$emailOrPhone' OR u.email = '$emailOrPhone' OR u.mobile_number = '$emailOrPhone')
        AND c.role = 'user'
        LIMIT 1
    ";

    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['last_name'] = $row['last_name'];
            $_SESSION['email'] = $row['user_email'];
            $_SESSION['mobile_number'] = $row['mobile_number'];
            $_SESSION['role'] = $row['role'];

            // Update last login
            $update = "UPDATE credentials SET last_login = NOW() WHERE credential_id = " . $row['credential_id'];
            mysqli_query($conn, $update);

            // Redirect to dashboard
            header("Location: userdashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ExchangeWise Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: white;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 40px;
            border-bottom: 1px solid #f8faf9;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            width: 30px;
            height: 30px;
            margin-right: 8px;
        }

        .logo span {
            font-weight: bold;
            font-size: 18px;
            color: #0f6a29;
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .nav a {
            text-decoration: none;
            color: black;
            font-size: 14px;
        }

        .login-btn {
            background: #0f6a29;
            color: white;
            border: none;
            padding: 6px 18px;
            border-radius: 15px;
            cursor: pointer;
            font-weight: bold;
        }

        /* Login card */
        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            width: 320px;
            border: 2px solid #0f6a29;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            background-color: #d9f0e6;
        }

        .card h2 {
            color: #0f6a29;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 14px;
            margin-bottom: 15px;
            color: #000;
        }

        .card label {
            display: block;
            font-weight: bold;
            font-size: 13px;
            text-align: left;
            margin: 8px 0 5px;
        }

        .card input[type="text"],
        .card input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            margin: 10px 0;
        }

        .remember-forgot label {
            display: flex;
            align-items: center;
            font-weight: normal;
        }

        .remember-forgot input {
            margin-right: 5px;
        }

        .remember-forgot a {
            color: #0f6a29;
            text-decoration: none;
        }

        .sign-in-btn {
            width: 100%;
            background: #0f6a29;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            margin: 12px 0;
        }

        .signup {
            font-size: 13px;
        }

        .signup a {
            color: #0f6a29;
            text-decoration: none;
            font-weight: bold;
        }

        .error {
            color: red;
            font-size: 13px;
            margin-bottom: 10px;
            text-align: center;
        }

        /* Footer */
        .footer {
            display: flex;
            justify-content: center;
            gap: 40px;
            font-size: 13px;
            padding: 10px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">
            <img src="logo.png" alt="Logo">
            <span>ExchangeWise</span>
        </div>
        <div class="nav">
            <a href="#">Products</a>
            <a href="#">Rates</a>
            <a href="#">Business</a>
            <a href="#">Learn</a>
            <button class="login-btn">Login</button>
        </div>
    </div>

    <!-- Login card -->
    <div class="container">
        <div class="card">
            <h2>Welcome back</h2>
            <p>Sign in to your exchangewise account</p>

            <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

            <form method="POST" action="">
                <label>Email or Phone Number</label>
                <input type="text" name="emailOrPhone" placeholder="Enter email or phone number" required>

                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>

                <div class="remember-forgot">
                    <label><input type="checkbox"> Remember me</label>
                    <a href="forgotPass.php">Forgot Password?</a>
                </div>

                <button type="submit" name="login" class="sign-in-btn">Sign In</button>
            </form>

            <div class="signup">
                Don't have an account? <a href="signup.php">Signup for free</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <span>Help & support</span>
        <span>Security</span>
        <span>Contact</span>
    </div>
</body>

</html>
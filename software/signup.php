<?php
// Include database connection
include 'db.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect form data
    $first_name       = $conn->real_escape_string($_POST['first_name']);
    $last_name        = $conn->real_escape_string($_POST['last_name']);
    $email            = $conn->real_escape_string($_POST['email']);
    $mobile_number    = $conn->real_escape_string($_POST['mobile_number']);
    $passport_number  = $conn->real_escape_string($_POST['passport_number']);
    $nid_number       = $conn->real_escape_string($_POST['nid_number']);
    $address          = $conn->real_escape_string($_POST['address']);
    $occupation       = $conn->real_escape_string($_POST['occupation']);
    $purpose          = $conn->real_escape_string($_POST['purpose']);
    $password         = $_POST['password'];
    $password_hash    = password_hash($password, PASSWORD_DEFAULT);

    // File upload handling
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $passport_file = "";
    if (!empty($_FILES['passport_file']['name'])) {
        $passport_file = $upload_dir . basename($_FILES['passport_file']['name']);
        move_uploaded_file($_FILES['passport_file']['tmp_name'], $passport_file);
    }

    $nid_file = "";
    if (!empty($_FILES['nid_file']['name'])) {
        $nid_file = $upload_dir . basename($_FILES['nid_file']['name']);
        move_uploaded_file($_FILES['nid_file']['tmp_name'], $nid_file);
    }

    $address_proof_file = "";
    if (!empty($_FILES['address_proof_file']['name'])) {
        $address_proof_file = $upload_dir . basename($_FILES['address_proof_file']['name']);
        move_uploaded_file($_FILES['address_proof_file']['tmp_name'], $address_proof_file);
    }

    $profile_picture = "";
    if (!empty($_FILES['profile_picture']['name'])) {
        $profile_picture = $upload_dir . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);
    }

    // Check if email already exists
    $check_email = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check_email->num_rows > 0) {
        echo "<script>alert('Email already registered! Please login.'); window.location.href='login.php';</script>";
        exit();
    }

    // Insert into users table
    $sql_user = "INSERT INTO users 
    (first_name, last_name, email, mobile_number, passport_number, passport_file, 
     nid_number, nid_file, address, address_proof_file, occupation, purpose, profile_picture, password_hash)
    VALUES (
        '$first_name', '$last_name', '$email', '$mobile_number', '$passport_number', '$passport_file',
        '$nid_number', '$nid_file', '$address', '$address_proof_file', '$occupation', '$purpose', '$profile_picture', '$password_hash'
    )";

    if ($conn->query($sql_user) === TRUE) {
        $user_id = $conn->insert_id;

        // Insert into credentials table
        $sql_credentials = "INSERT INTO credentials (email, password_hash, role, user_id)
                            VALUES ('$email', '$password_hash', 'user', '$user_id')";
        if ($conn->query($sql_credentials) === TRUE) {

            // 🪙 Create wallet and give $5 bonus
            $initial_balance = 5.00;
            $currency_code = 'USD';
            $sql_wallet = "INSERT INTO wallets (user_id, currency_code, balance)
                           VALUES ('$user_id', '$currency_code', '$initial_balance')";
            if ($conn->query($sql_wallet) === TRUE) {
                echo "<script>alert('Signup successful! You have received $5 in your wallet. Redirecting to your dashboard...'); window.location.href='userdashboard.php';</script>";
            } else {
                echo "Error creating wallet: " . $conn->error;
            }

        } else {
            echo "Error inserting credentials: " . $conn->error;
        }
    } else {
        echo "Error inserting user: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ExchangeWise Signup</title>
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

        /* Signup card */
        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 0;
        }

        .card {
            width: 400px;
            border: 2px solid #0f6a29;
            padding: 20px;
            border-radius: 8px;
            background-color: #d9f0e6;
        }

        .card h2 {
            color: #0f6a29;
            font-size: 20px;
            margin-bottom: 10px;
            text-align: center;
        }

        .card p {
            font-size: 14px;
            margin-bottom: 15px;
            color: #000;
            text-align: center;
        }

        .card label {
            display: block;
            font-weight: bold;
            font-size: 13px;
            margin: 8px 0 5px;
        }

        .card input,
        .card select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .sign-up-btn {
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

        .login-link {
            font-size: 13px;
            text-align: center;
        }

        .login-link a {
            color: #0f6a29;
            text-decoration: none;
            font-weight: bold;
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
        </div>
    </div>

    <!-- Signup card -->
    <div class="container">
        <div class="card">
            <h2>Create Account</h2>
            <p>Sign up to get started with ExchangeWise</p>

            <form method="POST" enctype="multipart/form-data">
                <label>First Name</label>
                <input type="text" name="first_name" placeholder="Enter first name" required>

                <label>Last Name</label>
                <input type="text" name="last_name" placeholder="Enter last name" required>

                <label>Email</label>
                <input type="email" name="email" placeholder="your@email.com" required>

                <label>Mobile Number</label>
                <input type="text" name="mobile_number" placeholder="+8801XXXXXXXXX" required>

                <label>Passport Number</label>
                <input type="text" name="passport_number" placeholder="Enter passport number" required>

                <label>Upload Passport File</label>
                <input type="file" name="passport_file">

                <label>NID Number</label>
                <input type="text" name="nid_number" placeholder="Enter NID number">

                <label>Upload NID File</label>
                <input type="file" name="nid_file">

                <label>Address</label>
                <input type="text" name="address" placeholder="Enter your address" required>

                <label>Upload Address Proof</label>
                <input type="file" name="address_proof_file">

                <label>Occupation</label>
                <select name="occupation" required>
                    <option value="">Select</option>
                    <option>Job</option>
                    <option>Business</option>
                    <option>Freelancer</option>
                    <option>Others</option>
                </select>

                <label>Purpose</label>
                <select name="purpose" required>
                    <option value="">Select</option>
                    <option>Travel</option>
                    <option>Education</option>
                    <option>Remittance</option>
                    <option>Online Payment</option>
                    <option>Others</option>
                </select>

                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>

                <button type="submit" class="sign-up-btn">Sign Up</button>
            </form>

            <div class="login-link">
                Already have an account? <a href="userlogin.php">Login here</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <a href="#">Help & support</a> |
        <a href="#">Security</a> |
        <a href="#">Contact</a>
    </div>
</body>

</html>

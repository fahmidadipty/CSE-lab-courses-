<?php
// Include database connection
include 'db.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect vendor form data
    $name           = $conn->real_escape_string($_POST['name']);
    $email          = $conn->real_escape_string($_POST['email']);
    $password       = $_POST['password'];
    $password_hash  = password_hash($password, PASSWORD_DEFAULT);
    $phone          = $conn->real_escape_string($_POST['phone']);
    $address        = $conn->real_escape_string($_POST['address']);
    $business_name  = $conn->real_escape_string($_POST['business_name']);

    // File upload handling (Profile Picture)
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $profile_picture = "";
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_file = $upload_dir . basename($_FILES['profile_picture']['name']);
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            $profile_picture = $target_file;
        }
    }

    // Check if email already exists in vendor table
    $check_email = $conn->prepare("SELECT * FROM vendor WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered!'); window.location.href='addvendor.php';</script>";
        exit();
    }

    // Insert into vendor table
    $sql_vendor = $conn->prepare("INSERT INTO vendor 
        (name, email, password_hash, phone, address, business_name, kyc_verified, status, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, 0, 'active', NOW(), NOW())");
    $sql_vendor->bind_param("ssssss", $name, $email, $password_hash, $phone, $address, $business_name);

    if ($sql_vendor->execute()) {
        // Get vendor_id of newly inserted vendor
        $vendor_id = $conn->insert_id;

        // Insert credentials for vendor login
        $sql_credentials = $conn->prepare("INSERT INTO credentials 
            (email, password_hash, role, vendor_id, created_at, updated_at)
            VALUES (?, ?, 'vendor', ?, NOW(), NOW())");
        $sql_credentials->bind_param("ssi", $email, $password_hash, $vendor_id);
        $sql_credentials->execute();

        echo "<script>alert('Vendor added successfully!'); window.location.href='admindashboard.php';</script>";
    } else {
        echo "Error inserting vendor: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Vendor - ExchangeWise</title>
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

        /* Card */
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
            <a href="admindashboard.php">Dashboard</a>
        </div>
    </div>

    <!-- Add Vendor Card -->
    <div class="container">
        <div class="card">
            <h2>Add New Vendor</h2>
            <p>Fill out the form to register a new vendor</p>

            <form method="POST" enctype="multipart/form-data">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="Enter vendor name" required>

                <label>Email</label>
                <input type="email" name="email" placeholder="vendor@email.com" required>

                <label>Password</label>
                <input type="password" name="password" placeholder="Enter password" required>

                <label>Phone</label>
                <input type="text" name="phone" placeholder="+8801XXXXXXXXX">

                <label>Address</label>
                <input type="text" name="address" placeholder="Enter address">

                <label>Business Name</label>
                <input type="text" name="business_name" placeholder="Enter business name">

                <label>Upload Profile Picture</label>
                <input type="file" name="profile_picture">

                <button type="submit" class="sign-up-btn">Add Vendor</button>
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
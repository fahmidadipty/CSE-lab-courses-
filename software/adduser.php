<?php
// Include database connection
include 'db.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize input
    $first_name  = $conn->real_escape_string($_POST['first_name']);
    $last_name   = $conn->real_escape_string($_POST['last_name']);
    $email       = $conn->real_escape_string($_POST['email']);
    $mobile      = $conn->real_escape_string($_POST['mobile']);
    $passport_number = $conn->real_escape_string($_POST['passport_number']);
    $nid_number  = $conn->real_escape_string($_POST['nid_number']);
    $address     = $conn->real_escape_string($_POST['address']);
    $occupation  = $conn->real_escape_string($_POST['occupation']);
    $purpose     = $conn->real_escape_string($_POST['purpose']);
    $password    = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // File upload paths
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    $passport_file = $nid_file = $address_proof_file = $profile_picture = NULL;

    // Handle file uploads
    if (!empty($_FILES['passport_file']['name'])) {
        $passport_file = $upload_dir . uniqid() . "_" . basename($_FILES['passport_file']['name']);
        move_uploaded_file($_FILES['passport_file']['tmp_name'], $passport_file);
    }
    if (!empty($_FILES['nid_file']['name'])) {
        $nid_file = $upload_dir . uniqid() . "_" . basename($_FILES['nid_file']['name']);
        move_uploaded_file($_FILES['nid_file']['tmp_name'], $nid_file);
    }
    if (!empty($_FILES['address_proof_file']['name'])) {
        $address_proof_file = $upload_dir . uniqid() . "_" . basename($_FILES['address_proof_file']['name']);
        move_uploaded_file($_FILES['address_proof_file']['tmp_name'], $address_proof_file);
    }
    if (!empty($_FILES['profile_picture']['name'])) {
        $profile_picture = $upload_dir . uniqid() . "_" . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture);
    }

    // Check if email already exists
    $check_email = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check_email->num_rows > 0) {
        echo "<script>alert('❌ Email already exists!'); window.location.href='ad_usermanagement.php';</script>";
        exit();
    }

    // Insert into database
    $sql = "INSERT INTO users 
    (first_name, last_name, email, mobile_number, passport_number, passport_file, nid_number, nid_file, address, address_proof_file, occupation, purpose, profile_picture, password_hash)
    VALUES 
    ('$first_name', '$last_name', '$email', '$mobile', '$passport_number', '$passport_file', '$nid_number', '$nid_file', '$address', '$address_proof_file', '$occupation', '$purpose', '$profile_picture', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('✅ User added successfully!'); window.location.href='ad_usermanagement.php';</script>";
    } else {
        echo "<script>alert('❌ Error: " . $conn->error . "');</script>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add User - ExchangeWise</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f8f9fb;
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

        .navbar .logo span {
            font-size: 20px;
            font-weight: bold;
            color: #0a74da;
        }

        .navbar .menu a {
            text-decoration: none;
            color: #000;
            font-size: 14px;
            margin-left: 20px;
        }

        .navbar .menu a:hover {
            color: #0a74da;
        }

        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }

        .card {
            width: 600px;
            border: 2px solid #0a74da;
            padding: 25px;
            border-radius: 10px;
            background-color: #eaf3ff;
        }

        h2 {
            color: #0a74da;
            text-align: center;
            margin-bottom: 10px;
        }

        p {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            font-size: 13px;
            display: block;
            margin: 8px 0 4px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 10px;
            font-size: 13px;
        }

        .submit-btn {
            width: 100%;
            background: #0a74da;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        .submit-btn:hover {
            background: #095bb3;
        }

        .back-link {
            text-align: center;
            margin-top: 10px;
        }

        .back-link a {
            color: #0a74da;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .footer {
            border-top: 1px solid #eee;
            text-align: center;
            padding: 15px;
            font-size: 13px;
            background: #f9f9f9;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo"><span>ExchangeWise</span></div>
        <div class="menu">
            <a href="admindashboard.php">Dashboard</a>
            <a href="ad_usermanagement.php">Users</a>
        </div>
    </div>

    <!-- Add User Form -->
    <div class="container">
        <div class="card">
            <h2>Add New User</h2>
            <p>Fill in the details below to register a new user.</p>

            <form method="POST" enctype="multipart/form-data">
                <label>First Name *</label>
                <input type="text" name="first_name" required>

                <label>Last Name *</label>
                <input type="text" name="last_name" required>

                <label>Email *</label>
                <input type="email" name="email" required>

                <label>Mobile Number *</label>
                <input type="text" name="mobile" required>

                <label>Passport Number *</label>
                <input type="text" name="passport_number" required>

                <label>Upload Passport File</label>
                <input type="file" name="passport_file" accept=".jpg,.jpeg,.png,.pdf">

                <label>NID Number *</label>
                <input type="text" name="nid_number" required>

                <label>Upload NID File</label>
                <input type="file" name="nid_file" accept=".jpg,.jpeg,.png,.pdf">

                <label>Address *</label>
                <textarea name="address" rows="2" required></textarea>

                <label>Upload Address Proof File</label>
                <input type="file" name="address_proof_file" accept=".jpg,.jpeg,.png,.pdf">

                <label>Occupation *</label>
                <select name="occupation" required>
                    <option value="">Select Occupation</option>
                    <option value="Job">Job</option>
                    <option value="Business">Business</option>
                    <option value="Freelancer">Freelancer</option>
                    <option value="Others">Others</option>
                </select>

                <label>Purpose *</label>
                <select name="purpose" required>
                    <option value="">Select Purpose</option>
                    <option value="Travel">Travel</option>
                    <option value="Education">Education</option>
                    <option value="Remittance">Remittance</option>
                    <option value="OnlinePayment">Online Payment</option>
                    <option value="Others">Others</option>
                </select>

                <label>Profile Picture</label>
                <input type="file" name="profile_picture" accept=".jpg,.jpeg,.png">

                <label>Password *</label>
                <input type="password" name="password" required>

                <button type="submit" class="submit-btn">Add User</button>
            </form>

            <div class="back-link">
                <a href="ad_usermanagement.php">⬅ Back to User List</a>
            </div>
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
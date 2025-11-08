<?php
session_start();
include 'db.php'; // Database connection

// Redirect if admin not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

// ========================== ADD USER FUNCTION ==========================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $mobile = $conn->real_escape_string($_POST['mobile']);
    $passport_number = $conn->real_escape_string($_POST['passport_number']);
    $nid_number = $conn->real_escape_string($_POST['nid_number']);
    $address = $conn->real_escape_string($_POST['address']);
    $occupation = $conn->real_escape_string($_POST['occupation']);
    $purpose = $conn->real_escape_string($_POST['purpose']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Create upload folder
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    // File upload handling
    function uploadFile($fileKey, $upload_dir)
    {
        if (!empty($_FILES[$fileKey]['name'])) {
            $filename = $upload_dir . uniqid() . "_" . basename($_FILES[$fileKey]['name']);
            move_uploaded_file($_FILES[$fileKey]['tmp_name'], $filename);
            return $filename;
        }
        return NULL;
    }

    $passport_file = uploadFile('passport_file', $upload_dir);
    $nid_file = uploadFile('nid_file', $upload_dir);
    $address_proof_file = uploadFile('address_proof_file', $upload_dir);
    $profile_picture = uploadFile('profile_picture', $upload_dir);

    // Check email duplicate
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        echo "<script>alert('❌ Email already exists!');</script>";
    } else {
        $sql = "INSERT INTO users (first_name, last_name, email, mobile_number, passport_number, passport_file, nid_number, nid_file, address, address_proof_file, occupation, purpose, profile_picture, password_hash)
                VALUES ('$first_name','$last_name','$email','$mobile','$passport_number','$passport_file','$nid_number','$nid_file','$address','$address_proof_file','$occupation','$purpose','$profile_picture','$password')";
        if ($conn->query($sql)) {
            echo "<script>alert('✅ User added successfully!');</script>";
        } else {
            echo "<script>alert('❌ Error adding user: " . $conn->error . "');</script>";
        }
    }
}

// ========================== FETCH USERS ==========================
$sql = "SELECT * FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ExchangeWise - User Management</title>
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
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar h2 {
            font-size: 20px;
            color: #0a74da;
            text-align: center;
            margin-bottom: 25px;
        }

        .nav {
            display: flex;
            flex-direction: column;
        }

        .nav a {
            text-decoration: none;
            color: #333;
            padding: 12px 10px;
            margin-bottom: 8px;
            border-radius: 8px;
            transition: .3s;
            font-weight: 500;
        }

        .nav a:hover,
        .nav a.active {
            background: #eaf3ff;
            color: #0a74da;
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
        }

        .buttons button {
            margin-left: 10px;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: .3s;
        }

        .add {
            background: #007bff;
            color: #fff;
        }

        .add:hover {
            background: #0056b3;
        }

        .table-section,
        .form-section {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #fafafa;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
        }

        .view {
            background: #eaf3ff;
            color: #0a74da;
        }

        .edit {
            background: #e2f0e8;
            color: #218838;
        }

        .delete {
            background: #f8d7da;
            color: #721c24;
        }

        .form-section h3 {
            margin-bottom: 15px;
            color: #0a74da;
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
            background: #0a74da;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
        }

        .submit-btn:hover {
            background: #095bb3;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div>
            <h2>ExchangeWise</h2>
            <div class="nav">
                <a href="admindashboard.php">🏠 Dashboard</a>
                <a href="reward.html">🎁 Rewards</a>
                <a href="managerate.html">💱 Manage Rates</a>
                <a href="admin_transaction.php">💳 Transactions</a>
                <a href="ad_usermanagement.php" class="active">👥 Users</a>
                <a href="ad_vandormanagement.php">🏬 Vendors</a>
                <a href="settings.html">⚙️ Settings</a>
                <a href="adminlogout.php">🚪 Logout</a>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="topbar">
            <h1>User Management</h1>
        </div>

        <!-- Add User Form -->
        <div class="form-section">
            <h3>➕ Add New User</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="add_user" value="1">
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
                <label>Upload Address Proof</label>
                <input type="file" name="address_proof_file" accept=".jpg,.jpeg,.png,.pdf">
                <label>Occupation *</label>
                <select name="occupation" required>
                    <option value="">Select</option>
                    <option>Job</option>
                    <option>Business</option>
                    <option>Freelancer</option>
                    <option>Others</option>
                </select>
                <label>Purpose *</label>
                <select name="purpose" required>
                    <option value="">Select</option>
                    <option>Travel</option>
                    <option>Education</option>
                    <option>Remittance</option>
                    <option>OnlinePayment</option>
                    <option>Others</option>
                </select>
                <label>Profile Picture</label>
                <input type="file" name="profile_picture" accept=".jpg,.jpeg,.png">
                <label>Password *</label>
                <input type="password" name="password" required>
                <button type="submit" class="submit-btn">Add User</button>
            </form>
        </div>

        <!-- User Table -->
        <div class="table-section">
            <h3>👥 Registered Users</h3>
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($u = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>U" . str_pad($u['user_id'], 5, '0', STR_PAD_LEFT) . "</td>
                                <td>{$u['first_name']} {$u['last_name']}</td>
                                <td>{$u['email']}</td>
                                <td>{$u['mobile_number']}</td>
                                <td>{$u['address']}</td>
                                <td>" . date('d M Y', strtotime($u['created_at'])) . "</td>
                                <td>
                                    <button class='btn view' onclick=\"window.location.href='view_user.php?id={$u['user_id']}'\">View</button>
                                    <button class='btn edit' onclick=\"window.location.href='edit_user.php?id={$u['user_id']}'\">Edit</button>
                                    <button class='btn delete' onclick=\"deleteUser({$u['user_id']})\">Delete</button>
                                </td>
                            </tr>";
                        }
                    } else echo "<tr><td colspan='7'>No users found.</td></tr>";
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function deleteUser(id) {
            if (confirm("Delete this user?")) {
                window.location.href = "ad_deleteuser.php?id=" + id;
            }
        }
    </script>
</body>

</html>
<?php
session_start();
include('db.php'); // Your DB connection

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: userlogin.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$user_q = mysqli_query($conn, "SELECT * FROM users WHERE user_id='$user_id'");
if (!$user_q) {
    die("Database error: " . mysqli_error($conn));
}
$user = mysqli_fetch_assoc($user_q);
if (!$user) die("User not found! (user_id: " . htmlspecialchars($user_id) . ")");

// Default values
$firstName = $user['first_name'] ?? '';
$lastName = $user['last_name'] ?? '';
$email = $user['email'] ?? '';
$mobile = $user['mobile_number'] ?? '';
$passportNumber = $user['passport_number'] ?? '';
$nidNumber = $user['nid_number'] ?? '';
$address = $user['address'] ?? '';
$occupation = $user['occupation'] ?? '';
$purpose = $user['purpose'] ?? '';
$profilePicture = $user['profile_picture'] ?? 'default.png';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName_new = mysqli_real_escape_string($conn, $_POST['firstName'] ?? '');
    $lastName_new = mysqli_real_escape_string($conn, $_POST['lastName'] ?? '');
    $email_new = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $mobile_new = mysqli_real_escape_string($conn, $_POST['mobile'] ?? '');
    $passportNumber_new = mysqli_real_escape_string($conn, $_POST['passportNumber'] ?? '');
    $nidNumber_new = mysqli_real_escape_string($conn, $_POST['nidNumber'] ?? '');
    $address_new = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
    $occupation_new = mysqli_real_escape_string($conn, $_POST['occupation'] ?? '');
    $purpose_new = mysqli_real_escape_string($conn, $_POST['purpose'] ?? '');

    $uploads_dir = __DIR__ . '/uploads/profiles';
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true);
    }

    $profile_uploaded_name = $profilePicture;

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_picture'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        if (in_array($ext, $allowed) && $file['size'] <= 2 * 1024 * 1024) {
            $profile_name = 'profile_' . $user_id . '.' . $ext;
            $target_path = $uploads_dir . '/' . $profile_name;

            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                @chmod($target_path, 0644);
                $profile_uploaded_name = $profile_name;
            }
        }
    }

    $email_sql = $email_new ? "'$email_new'" : "NULL";
    $mobile_sql = $mobile_new ? "'$mobile_new'" : "NULL";
    $profile_sql = $profile_uploaded_name ? "'" . mysqli_real_escape_string($conn, $profile_uploaded_name) . "'" : "NULL";

    $update_query = "
        UPDATE users SET 
            first_name='$firstName_new',
            last_name='$lastName_new',
            email=$email_sql,
            mobile_number=$mobile_sql,
            passport_number='$passportNumber_new',
            nid_number='$nidNumber_new',
            address='$address_new',
            occupation='$occupation_new',
            purpose='$purpose_new',
            profile_picture=$profile_sql,
            updated_at = CURRENT_TIMESTAMP
        WHERE user_id='$user_id'
    ";

    if (mysqli_query($conn, $update_query)) {
        $success = "Profile updated successfully!";
        $firstName = $firstName_new;
        $lastName = $lastName_new;
        $email = $email_new;
        $mobile = $mobile_new;
        $passportNumber = $passportNumber_new;
        $nidNumber = $nidNumber_new;
        $address = $address_new;
        $occupation = $occupation_new;
        $purpose = $purpose_new;
        $profilePicture = $profile_uploaded_name;
    } else {
        $error = "Failed to update profile: " . mysqli_error($conn);
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Profile Settings</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f4f7; margin:0; padding:0; }
        .navbar { background: #fff; padding: 15px 40px; border-bottom: 1px solid #ddd; display:flex; justify-content:space-between; align-items:center; }
        .navbar h2 { color: #0a802c; margin:0; font-size:20px; }
        .navbar a { text-decoration:none; background:#0a802c; color:#fff; padding:8px 18px; border-radius:8px; font-size:14px; }
        .navbar a:hover { background:#06691f; }
        .container { max-width:700px; margin:40px auto; background:#fff; padding:30px; border-radius:16px; box-shadow:0 5px 20px rgba(0,0,0,0.1); }
        label { display:block; margin-bottom:6px; font-weight:bold; }
        input[type=text], input[type=email], select, textarea { width:100%; padding:10px; margin-bottom:16px; border-radius:8px; border:1px solid #ccc; }
        button { padding:10px 20px; border:none; border-radius:8px; background:#0a802c; color:#fff; cursor:pointer; }
        button:hover { background:#06691f; }
        .avatar-preview { width:120px; height:120px; border-radius:50%; border:1px solid #ccc; margin-bottom:12px; overflow:hidden; }
        .avatar-preview img { width:100%; height:100%; object-fit:cover; }
        .success { color:green; margin-bottom:12px; }
        .error { color:red; margin-bottom:12px; }
    </style>
</head>
<body>

    <div class="navbar">
        <h2>Profile Settings</h2>
        <a href="userdashboard.php">Dashboard</a>
    </div>

    <div class="container">
        <h2>Edit Profile</h2>
        <?php if (isset($success)) echo "<div class='success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="avatar-preview">
                <?php if ($profilePicture && file_exists(__DIR__ . '/uploads/profiles/' . $profilePicture)): ?>
                    <img src="uploads/profiles/<?php echo htmlspecialchars($profilePicture); ?>" alt="Profile Picture">
                <?php else: ?>
                    <img src="uploads/profiles/default.png" alt="Profile Picture">
                <?php endif; ?>
            </div>
            <label>Profile Picture</label>
            <input type="file" name="profile_picture" accept="image/*">

            <label>First Name</label>
            <input type="text" name="firstName" value="<?php echo htmlspecialchars($firstName); ?>" required>

            <label>Last Name</label>
            <input type="text" name="lastName" value="<?php echo htmlspecialchars($lastName); ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">

            <label>Mobile Number</label>
            <input type="text" name="mobile" value="<?php echo htmlspecialchars($mobile); ?>">

            <label>Passport Number</label>
            <input type="text" name="passportNumber" value="<?php echo htmlspecialchars($passportNumber); ?>" required>

            <label>NID Number</label>
            <input type="text" name="nidNumber" value="<?php echo htmlspecialchars($nidNumber); ?>" required>

            <label>Address</label>
            <textarea name="address" required><?php echo htmlspecialchars($address); ?></textarea>

            <label>Occupation</label>
            <select name="occupation" required>
                <option value="Job" <?php if ($occupation == 'Job') echo 'selected'; ?>>Job</option>
                <option value="Business" <?php if ($occupation == 'Business') echo 'selected'; ?>>Business</option>
                <option value="Freelancer" <?php if ($occupation == 'Freelancer') echo 'selected'; ?>>Freelancer</option>
                <option value="Others" <?php if ($occupation == 'Others') echo 'selected'; ?>>Others</option>
            </select>

            <label>Purpose</label>
            <select name="purpose" required>
                <option value="Travel" <?php if ($purpose == 'Travel') echo 'selected'; ?>>Travel</option>
                <option value="Education" <?php if ($purpose == 'Education') echo 'selected'; ?>>Education</option>
                <option value="Remittance" <?php if ($purpose == 'Remittance') echo 'selected'; ?>>Remittance</option>
                <option value="OnlinePayment" <?php if ($purpose == 'OnlinePayment') echo 'selected'; ?>>Online Payment</option>
                <option value="Others" <?php if ($purpose == 'Others') echo 'selected'; ?>>Others</option>
            </select>

            <button type="submit">Save Changes</button>
        </form>
    </div>

</body>
</html>

<?php
session_start();
require_once 'db.php';

// ✅ Check admin login
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit();
}

// ✅ Validate vendor ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $vendor_id = intval($_GET['id']);

    // Check if vendor exists
    $check = $conn->prepare("SELECT vendor_id FROM vendor WHERE vendor_id = ?");
    $check->bind_param("i", $vendor_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        // ✅ Delete vendor safely
        $stmt = $conn->prepare("DELETE FROM vendor WHERE vendor_id = ?");
        $stmt->bind_param("i", $vendor_id);

        if ($stmt->execute()) {
            $_SESSION['success_msg'] = "Vendor deleted successfully.";
        } else {
            $_SESSION['error_msg'] = "Error deleting vendor. Please try again.";
        }
        $stmt->close();
    } else {
        $_SESSION['error_msg'] = "Vendor not found.";
    }

    $check->close();
} else {
    $_SESSION['error_msg'] = "Invalid vendor ID.";
}

// ✅ Redirect back to vendor management page
header("Location: ad_vendormanagement.php");
exit();
?>

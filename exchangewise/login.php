<?php
session_start();
include '../db.php'; // Include your database connection

// Check if the login form was submitted
if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare a query to fetch user details by email from Customers table
    $query = "SELECT * FROM Customers WHERE Email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user is found in Customers table
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['PasswordHash'])) {
            // Set session variables for customer
            $_SESSION['id'] = $user['CustomerID'];
            $_SESSION['email'] = $user['Email'];
            $_SESSION['name'] = $user['FirstName'];

            // Redirect to customer dashboard
            header("Location: cusdash.php");
            exit();
        } else {
            echo "Invalid password for customer!";
        }
    } else {
        // If no customer found, check in Admin table
        $query = "SELECT * FROM Admin WHERE Email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user is found in Admin table
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();

            // Verify the password for admin
            if ($password == $admin['PasswordHash']) {
                // Set session variables for admin
                $_SESSION['id'] = $admin['ID'];
                $_SESSION['email'] = $admin['Email'];
                $_SESSION['name'] = $admin['FirstName'];

                // Redirect to admin dashboard
                header("Location: admindash.php");
                exit();
            } else {
                echo "Invalid password for admin!";
            }
        } else {
            echo "No user found with this email in both Customer and Admin tables!";
        }
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <!-- Bootstrap CDN for responsive design -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-pzjw8f+ua7Kw1TIq0v8FqNfEXcWqHqWv8KhTK4fj4xk6xJfa+RwvX++dtzRiSJE/" crossorigin="anonymous">

    <style>
        /* Basic styling */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-image: url('background.jpg'); /* Set the homepage background image */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent background */
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-container label {
            font-size: 14px;
            margin-bottom: 5px;
            display: block;
        }

        .login-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .login-container input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .login-container input[type="submit"]:hover {
            background-color: #45a049;
        }

        .login-container p {
            text-align: center;
            font-size: 14px;
        }

        .login-container a {
            color: #4CAF50;
            text-decoration: none;
        }

        .login-container a:hover {
            text-decoration: underline;
        }

        /* Media Query for small devices */
        @media (max-width: 576px) {
            .login-container {
                padding: 20px;
                width: 90%;
            }
        }

    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <div>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required placeholder="Email">
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="passwordHash" name="password" required placeholder="Password">
            </div>
            <div>
                <input type="submit" name="submit" value="Login">
            </div>
        </form>
        <p>No account? <a href="reg.php">Signup Here</a></p>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zyxtOZ6gD5gtnD9gD6cJgH1Rb8EZp4jV0my93+X+5" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.5/dist/umd/popper.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0v8FqNfEXcWqHqWv8KhTK4fj4xk6xJfa+RwvX++dtzRiSJE/" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0v8FqNfEXcWqHqWv8KhTK4fj4xk6xJfa+RwvX++dtzRiSJE/" crossorigin="anonymous"></script>
</body>
</html>

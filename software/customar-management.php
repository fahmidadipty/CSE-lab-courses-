<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExchangeWise - Customer Management</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            display: flex;
            background-color: #f4f6f8;
            color: #333;
            height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: #fff;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #265282;
        }

        .nav {
            display: flex;
            flex-direction: column;
        }

        .nav a {
            padding: 15px 25px;
            color: #333;
            text-decoration: none;
            transition: 0.3s;
        }

        .nav a:hover,
        .nav a.active {
            background: #eaf1ff;
            color: #265282;
        }

        .sidebar .bottom {
            padding: 15px 25px;
            background: #f8f9fa;
            border-top: 1px solid #ddd;
            text-align: center;
        }

        /* Main Section */
        .main {
            flex: 1;
            padding: 25px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
        }

        .search-bar {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-bar input {
            padding: 10px 15px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .search-bar button {
            padding: 10px 20px;
            border: none;
            background: #265282;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            text-align: left;
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f1f5fb;
            color: #265282;
        }

        tr:hover {
            background: #f9faff;
        }

        .actions button {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .view {
            background: #007bff;
            color: white;
        }

        .delete {
            background: #dc3545;
            color: white;
        }

        footer {
            text-align: center;
            padding: 15px;
            font-size: 14px;
            color: #888;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <h2>ExchangeWise</h2>
            <div class="nav">
                <a href="vendordashboard.php">🏠 Dashboard</a>
                <a href="transaction-vendor.php">💳 Transactions</a>
                <a href="customar-management.php" class="active">👥 Customers</a>
                <a href="VendorAnalytics.html">📊 Analytics</a>
                <a href="VendorReports.html">📁 Reports</a>
                <a href="settings.html">⚙️ Settings</a>
                <a href="logout_admin.html">Logout</a>
            </div>
        </div>
        <div class="bottom">
            <strong>Global Exchange</strong><br>
            Admin Panel
        </div>
    </div>

    <!-- Main Content -->
    <div class="main">
        <div class="header">
            <h1>Customer Management</h1>
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search customers...">
                <button onclick="searchCustomer()">Search</button>
            </div>
        </div>

        <div class="card">
            <h3>Registered Customers</h3>
            <table id="customerTable">
                <thead>
                    <tr>
                        <th>Customer ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Total Transactions</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>CUST001</td>
                        <td>Rahim Uddin</td>
                        <td>rahim@example.com</td>
                        <td>017XXXXXXXX</td>
                        <td>12</td>
                        <td style="color:green;">Active</td>
                        <td class="actions">
                            <button class="view">View</button>
                            <button class="delete">Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>CUST002</td>
                        <td>Fahmida Dipty</td>
                        <td>dipty@example.com</td>
                        <td>018XXXXXXXX</td>
                        <td>8</td>
                        <td style="color:green;">Active</td>
                        <td class="actions">
                            <button class="view">View</button>
                            <button class="delete">Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>CUST003</td>
                        <td>Tanvir Hasan</td>
                        <td>tanvir@example.com</td>
                        <td>016XXXXXXXX</td>
                        <td>5</td>
                        <td style="color:red;">Suspended</td>
                        <td class="actions">
                            <button class="view">View</button>
                            <button class="delete">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <footer>
            © 2025 ExchangeWise Admin Portal. All rights reserved.
        </footer>
    </div>

    <script>
        function searchCustomer() {
            const input = document.getElementById("searchInput").value.toLowerCase();
            const rows = document.querySelectorAll("#customerTable tbody tr");

            rows.forEach(row => {
                const name = row.cells[1].textContent.toLowerCase();
                const email = row.cells[2].textContent.toLowerCase();
                if (name.includes(input) || email.includes(input)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }
    </script>

</body>

</html>
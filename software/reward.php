<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ExchangeWise Rewards | Admin Panel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background: #f5f7fa;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: #fff;
            border-right: 1px solid #e0e0e0;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
        }

        .sidebar h2 {
            text-align: center;
            color: #2b7a78;
            font-size: 22px;
            margin-bottom: 30px;
        }

        .nav a {
            text-decoration: none;
            color: #333;
            padding: 14px 25px;
            display: block;
            transition: background 0.2s;
        }

        .nav a:hover,
        .nav a.active {
            background: #e6f4f1;
            color: #2b7a78;
        }

        /* Main Area */
        .main-content {
            flex: 1;
            padding: 25px;
            overflow-y: auto;
        }

        .header {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 25px;
        }

        /* Cards */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .card h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        .card p {
            font-size: 24px;
            font-weight: 600;
            color: #2b7a78;
        }

        .card small {
            color: #777;
        }

        /* Table */
        .rewards-table {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .rewards-table h3 {
            margin-bottom: 15px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            overflow-x: auto;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        th {
            color: #666;
            font-weight: 600;
        }

        td {
            color: #333;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .status-active {
            color: #1e7e34;
            background: #d4edda;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 13px;
        }

        .status-expired {
            color: #fff;
            background: #343a40;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 13px;
        }

        .points-icon {
            color: #f0c419;
            margin-right: 5px;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .main-content {
                width: 100%;
            }

            table {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>ExchangeWise</h2>
        <div class="nav">
            <a href="admindashboard.php">🏠 Dashboard</a>
            <a href="reward.php" class="active">🎁 Rewards</a>
            <a href="managerate.php">💱 Manage Rates</a>
            <a href="admin_transaction.php">💳 Transactions</a>
            <a href="ad_usermanagement.php">👥 Users</a>
            <a href="ad_vandormanagement.php">🏬 Vendors</a>
            <a href="admin-profile-setting.php">⚙️ Settings</a>
            <a href="logout_admin.html">Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">Rewards Overview</div>

        <!-- Summary Cards -->
        <div class="cards">
            <div class="card">
                <h3>Total Rewards</h3>
                <p>5</p>
                <small>+3 new this month</small>
            </div>
            <div class="card">
                <h3>Total Claims</h3>
                <p>558</p>
                <small>+125 this week</small>
            </div>
            <div class="card">
                <h3>Expired Rewards</h3>
                <p>1</p>
                <small style="color:#e67e22;">Need review</small>
            </div>
        </div>

        <!-- Rewards Table -->
        <div class="rewards-table">
            <h3>All Rewards</h3>
            <table>
                <thead>
                    <tr>
                        <th>Reward Details</th>
                        <th>Category</th>
                        <th>Points Required</th>
                        <th>Claims</th>
                        <th>Valid Until</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>First Exchange Bonus</strong><br><small>Get 100 reward points for your first
                                currency exchange</small></td>
                        <td>Exchange</td>
                        <td><span class="points-icon">🪙</span>0</td>
                        <td>145/1000<br><small>14% claimed</small></td>
                        <td>2024-12-31</td>
                        <td><span class="status-active">Active</span></td>
                    </tr>
                    <tr>
                        <td><strong>5% Cashback on Large Exchanges</strong><br><small>Earn 5% cashback on exchanges over
                                $1000</small></td>
                        <td>Cashback</td>
                        <td><span class="points-icon">🪙</span>0</td>
                        <td>89/500<br><small>18% claimed</small></td>
                        <td>2024-10-31</td>
                        <td><span class="status-active">Active</span></td>
                    </tr>
                    <tr>
                        <td><strong>VIP Exchange Rate</strong><br><small>Access to premium exchange rates for 30
                                days</small></td>
                        <td>VIP</td>
                        <td><span class="points-icon">🪙</span>2000</td>
                        <td>23/100<br><small>23% claimed</small></td>
                        <td>2024-12-18</td>
                        <td><span class="status-active">Active</span></td>
                    </tr>
                    <tr>
                        <td><strong>Free Transaction Fee</strong><br><small>Waive transaction fees for your next 5
                                exchanges</small></td>
                        <td>Special</td>
                        <td><span class="points-icon">🪙</span>500</td>
                        <td>67/200<br><small>34% claimed</small></td>
                        <td>2024-11-30</td>
                        <td><span class="status-active">Active</span></td>
                    </tr>
                    <tr>
                        <td><strong>Double Points Weekend</strong><br><small>Earn double points on all weekend
                                transactions</small></td>
                        <td>Special</td>
                        <td><span class="points-icon">🪙</span>0</td>
                        <td>234/1000<br><small>23% claimed</small></td>
                        <td>2024-08-18</td>
                        <td><span class="status-expired">Expired</span></td>
                    </tr>
                </tbody>
            </table>
        </div>


    </div>

</body>

</html>
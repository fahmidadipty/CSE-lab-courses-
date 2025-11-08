<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExchangeWise - Settings</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            background-color: #f8f9fb;
            color: #333;
        }

        /* Sidebar */
        .sidebar {
            width: 230px;
            background-color: #fff;
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
            margin-bottom: 25px;
            text-align: center;
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
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
            font-weight: 500;
        }

        .nav a:hover,
        .nav a.active {
            background: #eaf3ff;
            color: #0a74da;
        }

        /* Content */
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

        /* Settings Sections */
        .settings-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
        }

        .card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .card h3 {
            margin-bottom: 15px;
            color: #0a74da;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
            font-weight: 600;
        }

        .save-btn {
            background: #007bff;
            color: #fff;
        }

        .save-btn:hover {
            background: #0056b3;
        }

        .reset-btn {
            background: #f0f0f0;
        }

        .reset-btn:hover {
            background: #e2e2e2;
        }

        /* Toggle Switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #0a74da;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        .switch-label {
            margin-left: 10px;
        }

        .toggle-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div>
            <h2>ExchangeWise</h2>
            <div class="nav">
                <a href="Admindashboard.html">🏠 Dashboard</a>
                <a href="reward.html">🎁 Rewards</a>
                <a href="managerate.html">💱 Manage Rates</a>
                <a href="Transactions_admin.html">💳 Transactions</a>
                <a href="usermanagement.html">👥 Users</a>
                <a href="vandormanagement.html">🏬 Vendors</a>
                <a href="settings.html" class="active">⚙️ Settings</a>
                <a href="logout_admin.html">Logout</a>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="topbar">
            <h1>Settings</h1>
        </div>

        <div class="settings-container">
            <!-- Profile Settings -->
            <div class="card">
                <h3>👤 Profile Settings</h3>
                <form>
                    <label for="name">Admin Name</label>
                    <input type="text" id="name" value="Fahmida Dipty">

                    <label for="email">Email</label>
                    <input type="email" id="email" value="admin@exchangewise.com">

                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" value="+880 17XXXXXXXX">

                    <button type="submit" class="btn save-btn">Save Changes</button>
                </form>
            </div>

            <!-- System Preferences -->
            <div class="card">
                <h3>💻 System Preferences</h3>

                <label for="theme">Theme</label>
                <select id="theme">
                    <option>Light</option>
                    <option>Dark</option>
                    <option>Auto</option>
                </select>

                <div class="toggle-row">
                    <span>Email Notifications</span>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="toggle-row">
                    <span>Push Notifications</span>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="toggle-row">
                    <span>Auto Backup</span>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>

                <button class="btn save-btn">Update Preferences</button>
            </div>

            <!-- Security Settings -->
            <div class="card">
                <h3>🔒 Security</h3>
                <form>
                    <label for="current">Current Password</label>
                    <input type="password" id="current" placeholder="Enter current password">

                    <label for="new">New Password</label>
                    <input type="password" id="new" placeholder="Enter new password">

                    <label for="confirm">Confirm New Password</label>
                    <input type="password" id="confirm" placeholder="Confirm new password">

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn save-btn">Update Password</button>
                        <button type="reset" class="btn reset-btn">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
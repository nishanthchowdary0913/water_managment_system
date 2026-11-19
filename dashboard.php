<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

include "db_connect.php";

// Get complaint statistics
$sql = "SELECT
            COUNT(*) AS total,
            COALESCE(SUM(status = 'Pending'), 0) AS pending,
            COALESCE(SUM(status = 'In Progress'), 0) AS in_progress,
            COALESCE(SUM(status = 'Resolved'), 0) AS resolved
        FROM complaints";

$result = $conn->query($sql);
$stats = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="css/style.css">

    <style>
        .welcome {
            background: #e0f2fe;
            padding: 22px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .welcome h2 {
            color: #0369a1;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 18px;
        }

        .stat-card {
            background: white;
            padding: 22px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .stat-card h2 {
            font-size: 30px;
            color: #0284c7;
        }

        .stat-card p {
            font-weight: bold;
        }

        .manage-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-top: 20px;
        }

        .manage-card {
            background: white;
            padding: 25px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .manage-card h3 {
            color: #0369a1;
            margin-bottom: 15px;
        }

        .logout-area {
            text-align: center;
            margin-top: 30px;
        }

        .logout-button {
            background: #dc2626;
        }

        .logout-button:hover {
            background: #b91c1c;
        }
    </style>
</head>

<body>

<header>
    <h1>Village Water Management System</h1>
    <p>Kotha Vellanti - Admin Panel</p>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_facilities.php">Facilities</a>
    <a href="manage_availability.php">Availability</a>
    <a href="manage_complaints.php">Complaints</a>
</nav>

<div class="container">

    <div class="welcome">
        <h2>
            Welcome,
            <?php echo htmlspecialchars($_SESSION["admin_username"]); ?>!
        </h2>

        <p>You can manage village water facilities, drinking water availability and resident complaints here.</p>
    </div>

    <h2 style="color:#0369a1; margin-bottom:20px;">
        Complaint Summary
    </h2>

    <div class="stats">

        <div class="stat-card">
            <h2><?php echo (int)$stats["total"]; ?></h2>
            <p>Total Complaints</p>
        </div>

        <div class="stat-card">
            <h2><?php echo (int)$stats["pending"]; ?></h2>
            <p>Pending</p>
        </div>

        <div class="stat-card">
            <h2><?php echo (int)$stats["in_progress"]; ?></h2>
            <p>In Progress</p>
        </div>

        <div class="stat-card">
            <h2><?php echo (int)$stats["resolved"]; ?></h2>
            <p>Resolved</p>
        </div>

    </div>

    <h2 style="color:#0369a1; margin:35px 0 20px;">
        Manage Water Management System
    </h2>

    <div class="manage-links">

        <div class="manage-card">
            <h3>Water Facilities</h3>
            <p>Add, update and delete village water facilities.</p>
            <a href="manage_facilities.php" class="btn">
                Manage Facilities
            </a>
        </div>

        <div class="manage-card">
            <h3>Water Availability</h3>
            <p>Update drinking water supply information.</p>
            <a href="manage_availability.php" class="btn">
                Manage Availability
            </a>
        </div>

        <div class="manage-card">
            <h3>Resident Complaints</h3>
            <p>View complaints and update their status.</p>
            <a href="manage_complaints.php" class="btn">
                Manage Complaints
            </a>
        </div>

    </div>

    <div class="logout-area">
        <a href="logout.php" class="btn logout-button">
            Admin Logout
        </a>
    </div>

</div>

<footer>
    <p>Village Water Management System - Kotha Vellanti</p>
</footer>

</body>
</html>
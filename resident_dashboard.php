<?php
session_start();

if (!isset($_SESSION["resident_id"])) {
    header("Location: resident_login.php");
    exit();
}

include "db_connect.php";

// Get resident details
$stmt = $conn->prepare(
    "SELECT full_name, phone FROM residents WHERE id = ?"
);

$stmt->bind_param("i", $_SESSION["resident_id"]);
$stmt->execute();

$resident = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$resident) {
    session_destroy();
    header("Location: resident_login.php");
    exit();
}

// Count complaints
$stmt = $conn->prepare(
    "SELECT
        COUNT(*) AS total,
        COALESCE(SUM(status = 'Pending'), 0) AS pending,
        COALESCE(SUM(status = 'In Progress'), 0) AS in_progress,
        COALESCE(SUM(status = 'Resolved'), 0) AS resolved
     FROM complaints
     WHERE phone = ?"
);

$stmt->bind_param("s", $resident["phone"]);
$stmt->execute();

$counts = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resident Dashboard</title>

    <link rel="stylesheet" href="css/style.css">

    <style>
        .welcome {
            text-align: center;
            margin-bottom: 30px;
        }

        .welcome h2 {
            color: #0369a1;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .dashboard-card {
            background: white;
            padding: 25px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .dashboard-card h3 {
            color: #0369a1;
            margin-bottom: 12px;
        }

        .dashboard-card p {
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 30px;
            font-weight: bold;
            color: #0284c7;
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
    <p>Kotha Vellanti</p>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="about.php">About</a>
    <a href="facilities.php">Facilities</a>
    <a href="availability.php">Availability</a>
    <a href="my_complaints.php">My Complaints</a>
</nav>

<div class="container">

    <div class="welcome">
        <h2>
            Welcome, <?php echo htmlspecialchars($resident["full_name"]); ?>!
        </h2>

        <p>Welcome to your Village Water Management Account.</p>
        <p>Manage your water-related services from here.</p>
    </div>

    <h2 style="color:#0369a1; margin-bottom:20px;">
        Resident Services
    </h2>

    <div class="dashboard-grid">

        <div class="dashboard-card">
            <h3>Water Facilities</h3>
            <p>View village water facilities and their details.</p>
            <a href="facilities.php" class="btn">View Facilities</a>
        </div>

        <div class="dashboard-card">
            <h3>Water Availability</h3>
            <p>Check drinking water supply in your area.</p>
            <a href="availability.php" class="btn">Check Availability</a>
        </div>

        <div class="dashboard-card">
            <h3>Submit Complaint</h3>
            <p>Report drinking water and water supply problems.</p>
            <a href="complaint.php" class="btn">Submit Complaint</a>
        </div>

        <div class="dashboard-card">
            <h3>My Complaints</h3>
            <p>Track the status of your submitted complaints.</p>
            <a href="my_complaints.php" class="btn">View My Complaints</a>
        </div>

    </div>

    <h2 style="color:#0369a1; margin:35px 0 20px;">
        Complaint Summary
    </h2>

    <div class="dashboard-grid">

        <div class="dashboard-card">
            <h3>Total Complaints</h3>
            <p class="stat-number">
                <?php echo (int)$counts["total"]; ?>
            </p>
        </div>

        <div class="dashboard-card">
            <h3>Pending</h3>
            <p class="stat-number">
                <?php echo (int)$counts["pending"]; ?>
            </p>
        </div>

        <div class="dashboard-card">
            <h3>In Progress</h3>
            <p class="stat-number">
                <?php echo (int)$counts["in_progress"]; ?>
            </p>
        </div>

        <div class="dashboard-card">
            <h3>Resolved</h3>
            <p class="stat-number">
                <?php echo (int)$counts["resolved"]; ?>
            </p>
        </div>

    </div>

    <div class="logout-area">
        <a href="resident_logout.php" class="btn logout-button">
            Logout
        </a>
    </div>

</div>

<footer>
    <p>Village Water Management System - Kotha Vellanti</p>
</footer>

</body>
</html>
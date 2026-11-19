<?php
include "db_connect.php";

// Count total water facilities
$facilityResult = $conn->query(
    "SELECT COUNT(*) AS total FROM water_facilities"
);
$facilities = $facilityResult->fetch_assoc()["total"];

// Count available water areas
$availabilityResult = $conn->query(
    "SELECT COUNT(*) AS total FROM water_availability
     WHERE supply_status = 'Available'"
);
$available = $availabilityResult->fetch_assoc()["total"];

// Count total complaints
$complaintResult = $conn->query(
    "SELECT COUNT(*) AS total FROM complaints"
);
$complaints = $complaintResult->fetch_assoc()["total"];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Village Water Management System</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- External CSS file -->
    <link rel="stylesheet" href="css/style.css">

    <style>
        .welcome {
            text-align: center;
            padding: 40px 15px;
        }

        .welcome h2 {
            color: #0369a1;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            max-width: 1000px;
            margin: 20px auto;
            padding: 15px;
        }

        .number {
            font-size: 32px;
            font-weight: bold;
            color: #075985;
        }

        .links {
            text-align: center;
            padding: 25px;
        }

        .links a {
            display: inline-block;
            margin: 8px;
        }

        .hero {
            background: #e0f2fe;
            padding: 25px;
            border-radius: 10px;
            max-width: 1000px;
            margin: auto;
        }
    </style>
</head>

<body>

<header>
    <h1>Village Water Management System</h1>
    <p>Water Facilities and Drinking Water Availability</p>
    <p>Kotha Vellanti Village</p>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="about.php">About</a>
    <a href="facilities.php">Facilities</a>
    <a href="availability.php">Availability</a>
    <a href="complaint.php">Complaints</a>
    <a href="login.php">Admin Login</a>
    <a href="register.php">Resident Register</a>
    <a href="resident_login.php">Resident Login</a>
</nav>

<section class="welcome">

    <div class="hero">

        <h2>Welcome to Our Water Management System</h2>

        <p>
            This system provides information about village water facilities,
            drinking water availability and resident complaints.
        </p>

        <p>
            Our goal is to improve access to safe drinking water
            and manage village water resources effectively.
        </p>

    </div>

</section>

<section class="container">

    <h2 style="text-align:center;">Water Management Statistics</h2>

    <div class="stats">

        <div class="card">
            <h3>Total Water Facilities</h3>
            <p class="number">
                <?php echo (int)$facilities; ?>
            </p>
        </div>

        <div class="card">
            <h3>Areas with Available Supply</h3>
            <p class="number">
                <?php echo (int)$available; ?>
            </p>
        </div>

        <div class="card">
            <h3>Total Complaints</h3>
            <p class="number">
                <?php echo (int)$complaints; ?>
            </p>
        </div>

    </div>

</section>

<section class="links">

    <h2>Explore Our Services</h2>

    <a href="about.php" class="btn">About Village</a>

    <a href="facilities.php" class="btn">View Facilities</a>

    <a href="availability.php" class="btn">Check Water Availability</a>

    <a href="complaint.php" class="btn">Submit Complaint</a>

</section>

<footer>
    <p>© 2026 Village Water Management System</p>
    <p>Developed for Kotha Vellanti Village</p>
</footer>

</body>
</html>
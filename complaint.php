<?php
session_start();
include "db_connect.php";

// Only logged-in residents can submit complaints
if (!isset($_SESSION["resident_id"])) {
    header("Location: resident_login.php");
    exit();
}

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

$message = "";

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (
        !isset($_POST["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
    ) {
        die("Invalid request. Please try again.");
    }

    $area = trim($_POST["area"] ?? "");
    $type = trim($_POST["complaint_type"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($area == "" || $type == "" || $description == "") {

        $message = "Please fill all fields.";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO complaints
            (resident_name, phone, area, complaint_type, description, status)
            VALUES (?, ?, ?, ?, ?, 'Pending')"
        );

        $stmt->bind_param(
            "sssss",
            $resident["full_name"],
            $resident["phone"],
            $area,
            $type,
            $description
        );

        if ($stmt->execute()) {
            $message = "Complaint submitted successfully!";
        } else {
            $message = "Unable to submit complaint. Please try again.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Submit Water Complaint</title>

    <link rel="stylesheet" href="css/style.css">

    <style>
        .container {
            max-width: 550px;
            margin: 35px auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #0369a1;
            margin-bottom: 20px;
        }

        .message {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            color: #075985;
        }

        input[readonly] {
            background: #f1f5f9;
        }

        button {
            width: 100%;
            margin-top: 10px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #0369a1;
            text-decoration: none;
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
    <a href="resident_dashboard.php">Dashboard</a>
</nav>

<div class="container">

    <h2>Water Complaint Form</h2>

    <p class="message">
        <?php echo htmlspecialchars($message); ?>
    </p>

    <form method="POST">

        <label>Resident Name</label>

        <input type="text"
               value="<?php echo htmlspecialchars($resident["full_name"]); ?>"
               readonly>

        <label>Phone Number</label>

        <input type="text"
               value="<?php echo htmlspecialchars($resident["phone"]); ?>"
               readonly>

        <label>Area / Village</label>

        <input type="text"
               name="area"
               placeholder="Enter your area"
               required>

        <label>Complaint Type</label>

        <select name="complaint_type" required>

            <option value="">Select Complaint</option>

            <option value="Water Leakage">Water Leakage</option>

            <option value="No Water Supply">No Water Supply</option>

            <option value="Dirty Water">Dirty Water</option>

            <option value="Low Water Pressure">Low Water Pressure</option>

            <option value="Other">Other</option>

        </select>

        <label>Complaint Description</label>

        <textarea name="description"
                  placeholder="Describe your problem"
                  rows="5"
                  required></textarea>

        <input type="hidden"
               name="csrf_token"
               value="<?php echo htmlspecialchars($_SESSION["csrf_token"]); ?>">

        <button type="submit">Submit Complaint</button>

    </form>

    <a href="resident_dashboard.php" class="back-link">
        Back to Resident Dashboard
    </a>

</div>

<footer>
    <p>Village Water Management System</p>
    <p>Developed for Kotha Vellanti Village</p>
</footer>

</body>
</html>
<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

include "db_connect.php";

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (
        !isset($_POST["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
    ) {
        die("Invalid request. Please try again.");
    }

    $complaint_id = filter_input(
        INPUT_POST,
        "complaint_id",
        FILTER_VALIDATE_INT
    );

    $status = $_POST["status"] ?? "";

    $allowed_statuses = [
        "Pending",
        "In Progress",
        "Resolved"
    ];

    if (!$complaint_id || !in_array($status, $allowed_statuses, true)) {
        $message = "Invalid complaint details.";
    } else {
        $stmt = $conn->prepare(
            "UPDATE complaints SET status = ? WHERE id = ?"
        );

        $stmt->bind_param("si", $status, $complaint_id);

        if ($stmt->execute()) {
            $message = "Complaint status updated successfully!";
        } else {
            $message = "Unable to update complaint status.";
        }

        $stmt->close();
    }
}

$result = $conn->query(
    "SELECT * FROM complaints ORDER BY created_at DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Complaints - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header>
    <h1>Village Water Management System</h1>
    <p>Admin Complaint Management</p>
</header>

<nav>
    <a href="index.php">Home</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="manage_facilities.php">Facilities</a>
    <a href="manage_availability.php">Availability</a>
    <a href="manage_complaints.php">Complaints</a>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">

    <div class="card">
        <h2>Manage Resident Complaints</h2>

        <?php if ($message != "") { ?>
            <p style="color:green; font-weight:bold;">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php } ?>

        <div style="overflow-x:auto;">

            <table>
                <tr>
                    <th>ID</th>
                    <th>Resident Name</th>
                    <th>Phone</th>
                    <th>Area</th>
                    <th>Complaint Type</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Update</th>
                </tr>

                <?php if ($result && $result->num_rows > 0) { ?>

                    <?php while ($row = $result->fetch_assoc()) { ?>

                        <tr>
                            <td><?php echo (int)$row["id"]; ?></td>

                            <td><?php echo htmlspecialchars($row["resident_name"]); ?></td>

                            <td><?php echo htmlspecialchars($row["phone"]); ?></td>

                            <td><?php echo htmlspecialchars($row["area"]); ?></td>

                            <td><?php echo htmlspecialchars($row["complaint_type"]); ?></td>

                            <td><?php echo htmlspecialchars($row["description"]); ?></td>

                            <td>
                                <?php echo htmlspecialchars($row["status"]); ?>
                            </td>

                            <td>
                                <form method="POST">

                                    <input type="hidden"
                                           name="csrf_token"
                                           value="<?php echo htmlspecialchars($_SESSION["csrf_token"]); ?>">

                                    <input type="hidden"
                                           name="complaint_id"
                                           value="<?php echo (int)$row["id"]; ?>">

                                    <select name="status">
                                        <option value="Pending"
                                            <?php if ($row["status"] == "Pending") echo "selected"; ?>>
                                            Pending
                                        </option>

                                        <option value="In Progress"
                                            <?php if ($row["status"] == "In Progress") echo "selected"; ?>>
                                            In Progress
                                        </option>

                                        <option value="Resolved"
                                            <?php if ($row["status"] == "Resolved") echo "selected"; ?>>
                                            Resolved
                                        </option>
                                    </select>

                                    <button type="submit">Update</button>

                                </form>
                            </td>
                        </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>
                        <td colspan="8">No complaints found.</td>
                    </tr>

                <?php } ?>

            </table>

        </div>

        <br>
        <a href="dashboard.php" class="btn">Back to Dashboard</a>

    </div>

</div>

<footer>
    <p>Village Water Management System</p>
</footer>

</body>
</html>
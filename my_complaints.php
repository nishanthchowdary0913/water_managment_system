<?php
session_start();

if (!isset($_SESSION["resident_id"])) {
    header("Location: resident_login.php");
    exit();
}

include "db_connect.php";

// Get registered resident phone number
$stmt = $conn->prepare(
    "SELECT phone FROM residents WHERE id = ?"
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

// Fetch only this resident's complaints
$stmt = $conn->prepare(
    "SELECT * FROM complaints WHERE phone = ? ORDER BY created_at DESC"
);

$stmt->bind_param("s", $resident["phone"]);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Complaints</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        h2 {
            color: #0369a1;
            margin-bottom: 15px;
        }

        .status {
            font-weight: bold;
        }

        .pending {
            color: #d97706;
        }

        .in-progress {
            color: #0284c7;
        }

        .resolved {
            color: #16a34a;
        }

        .table-container {
            overflow-x: auto;
        }

        .back-link {
            margin-top: 20px;
            display: inline-block;
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
    <a href="resident_dashboard.php">Dashboard</a>
    <a href="resident_logout.php">Logout</a>
</nav>

<div class="container">

    <div class="card">

        <h2>My Complaints</h2>

        <p>Here you can check the complaints you have submitted and their current status.</p>

        <div class="table-container">

            <table>
                <tr>
                    <th>Complaint Type</th>
                    <th>Area</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>

                <?php if ($result->num_rows > 0) { ?>

                    <?php while ($row = $result->fetch_assoc()) { ?>

                        <?php
                        $statusClass = "";

                        if ($row["status"] == "Pending") {
                            $statusClass = "pending";
                        } elseif ($row["status"] == "In Progress") {
                            $statusClass = "in-progress";
                        } elseif ($row["status"] == "Resolved") {
                            $statusClass = "resolved";
                        }
                        ?>

                        <tr>
                            <td><?php echo htmlspecialchars($row["complaint_type"]); ?></td>

                            <td><?php echo htmlspecialchars($row["area"]); ?></td>

                            <td><?php echo htmlspecialchars($row["description"]); ?></td>

                            <td class="status <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($row["status"]); ?>
                            </td>

                            <td><?php echo htmlspecialchars($row["created_at"]); ?></td>
                        </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>
                        <td colspan="5">
                            You have not submitted any complaints yet.
                        </td>
                    </tr>

                <?php } ?>

            </table>

        </div>

        <a href="resident_dashboard.php" class="btn back-link">
            Back to Dashboard
        </a>

    </div>

</div>

<footer>
    <p>Village Water Management System</p>
</footer>

</body>
</html>

<?php
$stmt->close();
?>
<?php
include "db_connect.php";

$sql = "SELECT * FROM water_availability ORDER BY updated_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Water Availability</title>

    <link rel="stylesheet" href="css/style.css">

    <style>
        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #0369a1;
            margin-bottom: 20px;
        }

        .table-container {
            overflow-x: auto;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        table {
            min-width: 650px;
        }

        .available {
            color: green;
            font-weight: bold;
        }

        .limited {
            color: orange;
            font-weight: bold;
        }

        .not-available {
            color: red;
            font-weight: bold;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
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

<div class="container">

    <h2>Drinking Water Availability</h2>

    <div class="table-container">

        <table>

            <tr>
                <th>Area</th>
                <th>Supply Status</th>
                <th>Supply Time</th>
                <th>Remarks</th>
                <th>Last Updated</th>
            </tr>

            <?php if ($result && $result->num_rows > 0) { ?>

                <?php while ($row = $result->fetch_assoc()) { ?>

                    <?php
                    $statusClass = "";

                    if ($row["supply_status"] == "Available") {
                        $statusClass = "available";
                    } elseif ($row["supply_status"] == "Limited") {
                        $statusClass = "limited";
                    } else {
                        $statusClass = "not-available";
                    }
                    ?>

                    <tr>

                        <td>
                            <?php echo htmlspecialchars($row["area"]); ?>
                        </td>

                        <td class="<?php echo $statusClass; ?>">
                            <?php echo htmlspecialchars($row["supply_status"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["supply_time"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["remarks"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($row["updated_at"]); ?>
                        </td>

                    </tr>

                <?php } ?>

            <?php } else { ?>

                <tr>
                    <td colspan="5">
                        No water availability information found.
                    </td>
                </tr>

            <?php } ?>

        </table>

    </div>

    <a href="index.php" class="btn back-link">Back to Home</a>

</div>

<footer>
    <p>Village Water Management System</p>
    <p>Developed for Kotha Vellanti Village</p>
</footer>

</body>
</html>
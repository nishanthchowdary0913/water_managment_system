<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $token = $_POST["csrf_token"] ?? "";

    if (!hash_equals($_SESSION["csrf_token"], $token)) {
        die("Invalid request. Please refresh the page.");
    }

    $id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
    $status = $_POST["status"] ?? "";

    $allowed_statuses = ["Pending", "In Progress", "Resolved"];

    if (!$id || !in_array($status, $allowed_statuses, true)) {
        $message = "Invalid complaint or status.";
    } else {

        $stmt = $conn->prepare(
            "UPDATE complaints SET status = ? WHERE id = ?"
        );

        $stmt->bind_param("si", $status, $id);

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
    <title>Manage Complaints</title>

    <style>
        body {
            font-family: Arial;
            background: #eaf6ff;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #0077b6;
        }

        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        th {
            background: #0077b6;
            color: white;
        }

        select {
            padding: 7px;
        }

        button {
            background: #0077b6;
            color: white;
            border: none;
            padding: 8px;
            cursor: pointer;
            margin-top: 5px;
        }

        .message {
            color: green;
            text-align: center;
        }
    </style>
</head>

<body>

<h2>Manage Resident Complaints</h2>

<p><a href="dashboard.php">Back to Dashboard</a></p>

<p class="message">
    <?php echo htmlspecialchars($message); ?>
</p>

<table>

<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Phone</th>
    <th>Area</th>
    <th>Complaint</th>
    <th>Description</th>
    <th>Status</th>
    <th>Update</th>
</tr>

<?php while ($row = $result->fetch_assoc()) { ?>

<tr>
    <td><?php echo htmlspecialchars($row["id"]); ?></td>
    <td><?php echo htmlspecialchars($row["resident_name"]); ?></td>
    <td><?php echo htmlspecialchars($row["phone"]); ?></td>
    <td><?php echo htmlspecialchars($row["area"]); ?></td>
    <td><?php echo htmlspecialchars($row["complaint_type"]); ?></td>
    <td><?php echo htmlspecialchars($row["description"]); ?></td>
    <td><?php echo htmlspecialchars($row["status"]); ?></td>

    <td>
        <form method="POST">

            <input type="hidden" name="id"
                   value="<?php echo (int)$row["id"]; ?>">

            <input type="hidden" name="csrf_token"
                   value="<?php echo htmlspecialchars($_SESSION["csrf_token"]); ?>">

            <select name="status">
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Resolved">Resolved</option>
            </select>

            <button type="submit">Update</button>

        </form>
    </td>
</tr>

<?php } ?>

</table>

</body>
</html>
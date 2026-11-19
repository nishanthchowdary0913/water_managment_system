<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include "db_connect.php";

// Add availability
if (isset($_POST['add_availability'])) {
    $area = trim($_POST['area']);
    $status = $_POST['supply_status'];
    $time = trim($_POST['supply_time']);
    $remarks = trim($_POST['remarks']);

    $allowed = ['Available', 'Limited', 'Not Available'];

    if ($area !== '' && in_array($status, $allowed, true)) {
        $stmt = $conn->prepare(
            "INSERT INTO water_availability
             (area, supply_status, supply_time, remarks)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param("ssss", $area, $status, $time, $remarks);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: manage_availability.php?added=1");
    exit();
}

// Update availability
if (isset($_POST['update_availability'])) {
    $id = (int)$_POST['availability_id'];
    $area = trim($_POST['area']);
    $status = $_POST['supply_status'];
    $time = trim($_POST['supply_time']);
    $remarks = trim($_POST['remarks']);

    $allowed = ['Available', 'Limited', 'Not Available'];

    if ($area !== '' && in_array($status, $allowed, true)) {
        $stmt = $conn->prepare(
            "UPDATE water_availability
             SET area=?, supply_status=?, supply_time=?, remarks=?
             WHERE id=?"
        );

        $stmt->bind_param(
            "ssssi", $area, $status, $time, $remarks, $id
        );

        $stmt->execute();
        $stmt->close();
    }

    header("Location: manage_availability.php?updated=1");
    exit();
}

// Delete availability
if (isset($_POST['delete_availability'])) {
    $id = (int)$_POST['availability_id'];

    $stmt = $conn->prepare(
        "DELETE FROM water_availability WHERE id=?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_availability.php?deleted=1");
    exit();
}

// Get record for editing
$edit = null;

if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];

    $stmt = $conn->prepare(
        "SELECT * FROM water_availability WHERE id=?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $edit = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Display all records
$result = $conn->query(
    "SELECT * FROM water_availability ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Water Availability</title>

    <link rel="stylesheet" href="css/style.css">

    <style>
        .page-title {
            color: #0369a1;
            margin-bottom: 20px;
        }

        .form-card {
            max-width: 750px;
            margin: auto;
        }

        .message {
            color: #047857;
            text-align: center;
            font-weight: bold;
            margin: 15px 0;
        }

        .table-container {
            overflow-x: auto;
        }

        .edit-button {
            background: #0284c7;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin: 3px;
        }

        .delete-button {
            background: #dc2626;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 3px;
        }

        .delete-button:hover {
            background: #b91c1c;
        }

        .cancel-link {
            margin-left: 12px;
            color: #0284c7;
        }

        .action-form {
            display: inline;
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

    <div class="card">

        <h2 class="page-title">Manage Water Availability</h2>

        <a href="dashboard.php" class="btn">Back to Dashboard</a>

        <?php
        if (isset($_GET['added'])) {
            echo '<p class="message">Water availability added successfully!</p>';
        }

        if (isset($_GET['updated'])) {
            echo '<p class="message">Water availability updated successfully!</p>';
        }

        if (isset($_GET['deleted'])) {
            echo '<p class="message">Water availability deleted successfully!</p>';
        }
        ?>

    </div>

    <div class="card form-card">

        <h2 class="page-title">
            <?php
            echo $edit ? "Edit Water Availability" : "Add Water Supply Information";
            ?>
        </h2>

        <form method="POST" action="manage_availability.php">

            <?php if ($edit) { ?>
                <input
                    type="hidden"
                    name="availability_id"
                    value="<?php echo (int)$edit['id']; ?>"
                >
            <?php } ?>

            <label>Village Area</label>
            <input
                type="text"
                name="area"
                placeholder="Enter Village Area"
                required
                value="<?php echo htmlspecialchars($edit['area'] ?? ''); ?>"
            >

            <label>Water Supply Status</label>
            <select name="supply_status" required>
                <option value="">Select Status</option>

                <?php
                $statuses = ['Available', 'Limited', 'Not Available'];

                foreach ($statuses as $status) {
                    $selected = (($edit['supply_status'] ?? '') === $status)
                        ? 'selected' : '';

                    echo "<option value='" . htmlspecialchars($status) . "' $selected>"
                        . htmlspecialchars($status) . "</option>";
                }
                ?>
            </select>

            <label>Supply Time</label>
            <input
                type="text"
                name="supply_time"
                placeholder="Example: 6 AM - 8 AM"
                value="<?php echo htmlspecialchars($edit['supply_time'] ?? ''); ?>"
            >

            <label>Remarks</label>
            <textarea
                name="remarks"
                rows="4"
                placeholder="Enter additional information"
            ><?php echo htmlspecialchars($edit['remarks'] ?? ''); ?></textarea>

            <?php if ($edit) { ?>

                <button type="submit" name="update_availability">
                    Save Changes
                </button>

                <a href="manage_availability.php" class="cancel-link">
                    Cancel
                </a>

            <?php } else { ?>

                <button type="submit" name="add_availability">
                    Add Availability
                </button>

            <?php } ?>

        </form>

    </div>

    <div class="card">

        <h2 class="page-title">Existing Water Availability</h2>

        <div class="table-container">

            <table>
                <tr>
                    <th>ID</th>
                    <th>Area</th>
                    <th>Status</th>
                    <th>Supply Time</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>

                <?php if ($result && $result->num_rows > 0) { ?>

                    <?php while ($row = $result->fetch_assoc()) { ?>

                        <tr>
                            <td><?php echo (int)$row['id']; ?></td>

                            <td><?php echo htmlspecialchars($row['area']); ?></td>

                            <td><?php echo htmlspecialchars($row['supply_status']); ?></td>

                            <td><?php echo htmlspecialchars($row['supply_time'] ?? ''); ?></td>

                            <td><?php echo htmlspecialchars($row['remarks'] ?? ''); ?></td>

                            <td>
                                <a
                                    class="edit-button"
                                    href="manage_availability.php?edit=<?php echo (int)$row['id']; ?>"
                                >
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="manage_availability.php"
                                    class="action-form"
                                    onsubmit="return confirm('Are you sure you want to delete this record?');"
                                >

                                    <input
                                        type="hidden"
                                        name="availability_id"
                                        value="<?php echo (int)$row['id']; ?>"
                                    >

                                    <button
                                        type="submit"
                                        name="delete_availability"
                                        class="delete-button"
                                    >
                                        Delete
                                    </button>

                                </form>
                            </td>
                        </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>
                        <td colspan="6">No availability records found.</td>
                    </tr>

                <?php } ?>

            </table>

        </div>

    </div>

</div>

<footer>
    <p>Village Water Management System - Kotha Vellanti</p>
</footer>

</body>
</html>
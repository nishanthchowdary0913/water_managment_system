<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include "db_connect.php";

// Delete facility
if (isset($_POST['delete_facility'])) {
    $id = (int)$_POST['facility_id'];

    $stmt = $conn->prepare(
        "DELETE FROM water_facilities WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_facilities.php?deleted=1");
    exit();
}

// Update facility
if (isset($_POST['update_facility'])) {
    $id = (int)$_POST['facility_id'];
    $name = trim($_POST['facility_name']);
    $type = trim($_POST['facility_type']);
    $location = trim($_POST['location']);
    $status = trim($_POST['status']);
    $description = trim($_POST['description']);

    $stmt = $conn->prepare(
        "UPDATE water_facilities
         SET facility_name=?, facility_type=?, location=?,
             status=?, description=?
         WHERE id=?"
    );

    $stmt->bind_param(
        "sssssi",
        $name, $type, $location, $status, $description, $id
    );

    $stmt->execute();
    $stmt->close();

    header("Location: manage_facilities.php?updated=1");
    exit();
}

// Add new facility
if (isset($_POST['add_facility'])) {
    $name = trim($_POST['facility_name']);
    $type = trim($_POST['facility_type']);
    $location = trim($_POST['location']);
    $status = trim($_POST['status']);
    $description = trim($_POST['description']);

    $stmt = $conn->prepare(
        "INSERT INTO water_facilities
         (facility_name, facility_type, location, status, description)
         VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "sssss",
        $name, $type, $location, $status, $description
    );

    $stmt->execute();
    $stmt->close();

    header("Location: manage_facilities.php?added=1");
    exit();
}

// Get facility for editing
$edit_facility = null;

if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];

    $stmt = $conn->prepare(
        "SELECT * FROM water_facilities WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $edit_facility = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Get all facilities
$result = $conn->query(
    "SELECT * FROM water_facilities ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Water Facilities</title>

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
            text-align: center;
            color: #047857;
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
            border-radius: 5px;
            border: none;
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

        <h2 class="page-title">Manage Water Facilities</h2>

        <p>
            <a href="dashboard.php" class="btn">Back to Dashboard</a>
        </p>

        <?php
        if (isset($_GET['added'])) {
            echo '<p class="message">Facility added successfully!</p>';
        }

        if (isset($_GET['updated'])) {
            echo '<p class="message">Facility updated successfully!</p>';
        }

        if (isset($_GET['deleted'])) {
            echo '<p class="message">Facility deleted successfully!</p>';
        }
        ?>

    </div>

    <div class="card form-card">

        <h2 class="page-title">
            <?php
            echo $edit_facility ? "Edit Facility" : "Add New Facility";
            ?>
        </h2>

        <form method="POST" action="manage_facilities.php">

            <?php if ($edit_facility) { ?>
                <input
                    type="hidden"
                    name="facility_id"
                    value="<?php echo (int)$edit_facility['id']; ?>"
                >
            <?php } ?>

            <label>Facility Name</label>
            <input
                type="text"
                name="facility_name"
                placeholder="Enter Facility Name"
                required
                value="<?php echo htmlspecialchars($edit_facility['facility_name'] ?? ''); ?>"
            >

            <label>Facility Type</label>
            <select name="facility_type" required>
                <option value="">Select Facility</option>

                <?php
                $types = [
                    'Public Water Tap',
                    'Water Tank',
                    'Borewell',
                    'Hand Pump',
                    'House Connection',
                    'Drinking Water Plant'
                ];

                foreach ($types as $type) {
                    $selected = (($edit_facility['facility_type'] ?? '') === $type)
                        ? 'selected' : '';

                    echo "<option value='" . htmlspecialchars($type) . "' $selected>"
                        . htmlspecialchars($type) . "</option>";
                }
                ?>
            </select>

            <label>Village Area / Location</label>
            <input
                type="text"
                name="location"
                placeholder="Enter Village Area"
                required
                value="<?php echo htmlspecialchars($edit_facility['location'] ?? ''); ?>"
            >

            <label>Facility Status</label>
            <select name="status" required>
                <option value="">Select Status</option>

                <?php
                $statuses = [
                    'Working',
                    'Under Maintenance',
                    'Not Working'
                ];

                foreach ($statuses as $status) {
                    $selected = (($edit_facility['status'] ?? '') === $status)
                        ? 'selected' : '';

                    echo "<option value='" . htmlspecialchars($status) . "' $selected>"
                        . htmlspecialchars($status) . "</option>";
                }
                ?>
            </select>

            <label>Description</label>
            <textarea
                name="description"
                rows="4"
                placeholder="Enter Facility Description"
            ><?php echo htmlspecialchars($edit_facility['description'] ?? ''); ?></textarea>

            <?php if ($edit_facility) { ?>

                <button type="submit" name="update_facility">
                    Save Changes
                </button>

                <a href="manage_facilities.php" class="cancel-link">
                    Cancel
                </a>

            <?php } else { ?>

                <button type="submit" name="add_facility">
                    Add Facility
                </button>

            <?php } ?>

        </form>

    </div>

    <div class="card">

        <h2 class="page-title">Existing Water Facilities</h2>

        <div class="table-container">

            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>

                <?php if ($result && $result->num_rows > 0) { ?>

                    <?php while ($row = $result->fetch_assoc()) { ?>

                        <tr>
                            <td><?php echo (int)$row['id']; ?></td>

                            <td><?php echo htmlspecialchars($row['facility_name']); ?></td>

                            <td><?php echo htmlspecialchars($row['facility_type']); ?></td>

                            <td><?php echo htmlspecialchars($row['location']); ?></td>

                            <td><?php echo htmlspecialchars($row['status']); ?></td>

                            <td>
                                <a
                                    class="edit-button"
                                    href="manage_facilities.php?edit=<?php echo (int)$row['id']; ?>"
                                >
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="manage_facilities.php"
                                    class="action-form"
                                    onsubmit="return confirm('Are you sure you want to delete this facility?');"
                                >

                                    <input
                                        type="hidden"
                                        name="facility_id"
                                        value="<?php echo (int)$row['id']; ?>"
                                    >

                                    <button
                                        class="delete-button"
                                        type="submit"
                                        name="delete_facility"
                                    >
                                        Delete
                                    </button>

                                </form>
                            </td>
                        </tr>

                    <?php } ?>

                <?php } else { ?>

                    <tr>
                        <td colspan="6">
                            No facilities found.
                        </td>
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
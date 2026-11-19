<?php
session_start();
include "db_connect.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT id, username, password FROM admin WHERE username = ?"
    );

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin["password"])) {

            session_regenerate_id(true);

            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_username"] = $admin["username"];

            header("Location: dashboard.php");
            exit();

        } else {
            $error = "Incorrect password!";
        }

    } else {
        $error = "Admin username not found!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>

    <link rel="stylesheet" href="css/style.css">

    <style>
        .login-container {
            max-width: 420px;
            margin: 60px auto;
        }

        .login-container h2 {
            text-align: center;
            color: #0369a1;
            margin-bottom: 20px;
        }

        .error {
            color: #dc2626;
            text-align: center;
            margin-bottom: 15px;
        }

        .login-link {
            text-align: center;
            margin-top: 18px;
        }

        .login-link a {
            color: #0284c7;
            text-decoration: none;
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
</nav>

<div class="container">

    <div class="card login-container">

        <h2>Admin Login</h2>

        <?php if ($error != "") { ?>
            <p class="error">
                <?php echo htmlspecialchars($error); ?>
            </p>
        <?php } ?>

        <form method="POST">

            <label>Admin Username</label>
            <input
                type="text"
                name="username"
                placeholder="Enter Admin Username"
                required
            >

            <label>Password</label>
            <input
                type="password"
                name="password"
                placeholder="Enter Admin Password"
                required
            >

            <button type="submit" style="width:100%;">
                Login
            </button>

        </form>

        <div class="login-link">
            <p><a href="index.php">Back to Home</a></p>
        </div>

    </div>

</div>

<footer>
    <p>Village Water Management System - Kotha Vellanti</p>
</footer>

</body>
</html>
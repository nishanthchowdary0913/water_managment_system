<?php
session_start();
include "db_connect.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT id, full_name, password FROM residents WHERE phone = ?"
    );

    $stmt->bind_param("s", $phone);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $resident = $result->fetch_assoc();

        if (password_verify($password, $resident["password"])) {

            session_regenerate_id(true);

            $_SESSION["resident_id"] = $resident["id"];
            $_SESSION["resident_name"] = $resident["full_name"];

            header("Location: resident_dashboard.php");
            exit();

        } else {
            $message = "Invalid phone number or password.";
        }

    } else {
        $message = "Invalid phone number or password.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Resident Login</title>

    <link rel="stylesheet" href="css/style.css">

    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .login-container {
            background: white;
            padding: 30px;
            width: 90%;
            max-width: 380px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.15);
        }

        h2 {
            text-align: center;
            color: #0369a1;
        }

        .message {
            text-align: center;
            color: red;
            margin-bottom: 12px;
        }

        button {
            width: 100%;
            margin-top: 10px;
        }

        .login-links {
            text-align: center;
            margin-top: 18px;
        }

        .login-links a {
            color: #0369a1;
            text-decoration: none;
        }

        .login-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<header>
    <h1>Village Water Management System</h1>
    <p>Kotha Vellanti Village</p>
</header>

<div class="login-container">

    <h2>Resident Login</h2>

    <p class="message">
        <?php echo htmlspecialchars($message); ?>
    </p>

    <form method="POST">

        <label>Phone Number</label>

        <input
            type="tel"
            name="phone"
            placeholder="Enter Phone Number"
            pattern="[0-9]{10}"
            maxlength="10"
            required
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            placeholder="Enter Password"
            required
        >

        <button type="submit">Login</button>

    </form>

    <div class="login-links">

        <p>New resident?</p>

        <p>
            <a href="register.php">Create Account</a>
        </p>

        <p>
            <a href="index.php">Back to Home</a>
        </p>

    </div>

</div>

<footer>
    <p>Village Water Management System</p>
    <p>Developed for Kotha Vellanti Village</p>
</footer>

</body>
</html>
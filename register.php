<?php
include "db_connect.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["full_name"]);
    $phone = trim($_POST["phone"]);
    $password = $_POST["password"];

    if ($name == "" || $phone == "" || $password == "") {
        $message = "Please fill all fields.";
    }
    elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $message = "Enter a valid 10-digit phone number.";
    }
    elseif (strlen($password) < 6) {
        $message = "Password must contain at least 6 characters.";
    }
    else {

        $check = $conn->prepare(
            "SELECT id FROM residents WHERE phone = ?"
        );

        $check->bind_param("s", $phone);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "This phone number is already registered.";
        } else {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO residents (full_name, phone, password)
                 VALUES (?, ?, ?)"
            );

            $stmt->bind_param(
                "sss",
                $name,
                $phone,
                $hashedPassword
            );

            if ($stmt->execute()) {
                $message = "Registration successful! You can now login.";
            } else {
                $message = "Registration failed. Please try again.";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resident Registration</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        .register-box {
            max-width: 450px;
            margin: 50px auto;
        }

        .register-box h2 {
            text-align: center;
            color: #0369a1;
            margin-bottom: 15px;
        }

        .message {
            text-align: center;
            color: #047857;
            margin-bottom: 12px;
        }

        .register-links {
            text-align: center;
            margin-top: 18px;
        }

        .register-links a {
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

<div class="container">

    <div class="card register-box">

        <h2>Resident Registration</h2>

        <?php if ($message != "") { ?>
            <p class="message">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php } ?>

        <form method="POST">

            <label>Full Name</label>
            <input
                type="text"
                name="full_name"
                placeholder="Enter Full Name"
                maxlength="100"
                required
            >

            <label>Phone Number</label>
            <input
                type="tel"
                name="phone"
                placeholder="Enter 10-digit Phone Number"
                pattern="[0-9]{10}"
                maxlength="10"
                required
            >

            <label>Create Password</label>
            <input
                type="password"
                name="password"
                placeholder="Create Password"
                minlength="6"
                required
            >

            <button type="submit" style="width:100%;">
                Register
            </button>

        </form>

        <div class="register-links">
            <p>Already registered?</p>
            <p><a href="resident_login.php">Resident Login</a></p>
            <p><a href="index.php">Back to Home</a></p>
        </div>

    </div>

</div>

<footer>
    <p>Village Water Management System</p>
</footer>

</body>
</html>
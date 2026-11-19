<?php
session_start();

// Remove resident session data
unset($_SESSION["resident_id"]);
unset($_SESSION["resident_name"]);

// Destroy session
session_destroy();

// Redirect to resident login
header("Location: resident_login.php");
exit();
?>
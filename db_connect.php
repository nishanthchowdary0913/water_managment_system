<?php

$host = getenv("DB_HOST") ?: "localhost";
$username = getenv("DB_USERNAME") ?: "root";
$password = getenv("DB_PASSWORD") !== false ? getenv("DB_PASSWORD") : "";
$database = getenv("DB_NAME") ?: "water_management";
$port = (int)(getenv("DB_PORT") ?: 3306);

mysqli_report(MYSQLI_REPORT_OFF);
$conn = @new mysqli($host, $username, $password, $database, $port);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    die("Service temporarily unavailable.");
}

$conn->set_charset("utf8mb4");
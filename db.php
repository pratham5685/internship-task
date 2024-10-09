<?php
// Database connection details
$servername = "localhost";
$username = "root"; // Default XAMPP MySQL username
$password = "";     // No password for MySQL in XAMPP by default
$dbname = "lead_form_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

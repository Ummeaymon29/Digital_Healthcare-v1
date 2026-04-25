<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "digital_healthcare");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
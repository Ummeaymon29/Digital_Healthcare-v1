<?php
$conn = mysqli_connect("localhost", "root", "", "digital_healthcare");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
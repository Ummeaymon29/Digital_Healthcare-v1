<?php
include 'db.php';

$fullname = $_POST['fullname'];
$email = $_POST['email'];
$password = $_POST['password'];
$role = $_POST['role'];

$sql = "INSERT INTO users (fullname, email, password, role)
        VALUES ('$fullname', '$email', '$password', '$role')";

if (mysqli_query($conn, $sql)) {
    echo "Registration Successful!";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
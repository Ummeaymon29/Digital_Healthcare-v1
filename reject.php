<?php
session_start();
include "db.php";

// Only doctors can reject
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: login.html"); 
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: doctor_dashboard.php?msg=Invalid Request&type=error"); 
    exit();
}

$consultation_id = (int)$_GET['id'];
$doctor_id = (int)$_SESSION['user_id'];

// Verify & Reject (only if this consultation belongs to this doctor)
$stmt = $conn->prepare("UPDATE consultations SET status='rejected' WHERE id=? AND doctor_id=?");
$stmt->bind_param("ii", $consultation_id, $doctor_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    header("Location: view_request.php?msg=Request rejected successfully&type=success");
} else {
    header("Location: view_request.php?msg=Failed to reject request&type=error");
}
exit();
?>
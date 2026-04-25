<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: login.html"); exit();
}

if (!isset($_GET['id'])) {
    header("Location: doctor_dashboard.php?msg=Invalid Request&type=error"); exit();
}

$consultation_id = (int)$_GET['id'];
$doctor_id = (int)$_SESSION['user_id'];

// Verify & Accept
$stmt = $conn->prepare("UPDATE consultations SET status='accepted' WHERE id=? AND doctor_id=?");
$stmt->bind_param("ii", $consultation_id, $doctor_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    header("Location: view_request.php?msg=Request accepted successfully&type=success");
} else {
    header("Location: view_request.php?msg=Failed to accept request&type=error");
}
exit();
?>
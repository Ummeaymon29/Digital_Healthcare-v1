<?php
session_start(); include "db.php";
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role'] ?? '') !== 'doctor') { header("Location: login.html?msg=Please+login+as+Doctor&type=error"); exit(); }
$consultation_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$doctor_id = (int)$_SESSION['user_id'];
if ($consultation_id <= 0) { header("Location: doctor_dashboard.php?msg=Invalid+Request&type=error"); exit(); }
$stmt = $conn->prepare("UPDATE consultations SET status='rejected' WHERE id=? AND doctor_id=?");
$stmt->bind_param("ii", $consultation_id, $doctor_id);
if ($stmt->execute() && $stmt->affected_rows > 0) { header("Location: doctor_dashboard.php?msg=Request+rejected+successfully&type=success"); } 
else { header("Location: doctor_dashboard.php?msg=Failed+to+reject+request&type=error"); }
exit();
?>
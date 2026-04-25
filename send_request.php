<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    header("Location: login.html"); exit();
}

$patient_id = (int)$_SESSION['user_id'];
$doctor_id  = (int)($_POST['doctor_id'] ?? 0);
$date       = $_POST['date'] ?? '';
$time       = $_POST['time'] ?? '';
$message    = trim($_POST['message'] ?? '');

if (!$doctor_id || empty($date) || empty($time) || empty($message)) {
    header("Location: patient_dashboard.php?msg=Please fill all fields&type=error"); exit();
}

$stmt = $conn->prepare("INSERT INTO consultations (patient_id, doctor_id, date, time, message, status) 
                        VALUES (?, ?, ?, ?, ?, 'pending')");
$stmt->bind_param("iisss", $patient_id, $doctor_id, $date, $time, $message);

if ($stmt->execute()) {
    header("Location: patient_dashboard.php?msg=Request sent successfully!&type=success");
} else {
    header("Location: patient_dashboard.php?msg=Error sending request. Please try again.&type=error");
}
exit();
?>
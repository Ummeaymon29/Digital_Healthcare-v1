<?php
session_start();
include "db.php";
if (!isset($_GET['id'])) {
    header("Location: doctor_dashboard.php?msg=Invalid+Request&type=error"); exit();
}
$consultation_id = (int)$_GET['id'];
$doctor_id = (int)$_SESSION['user_id'];

$stmt = $conn->prepare("UPDATE consultations SET status='completed', end_time=NOW() WHERE id=? AND doctor_id=?");
$stmt->bind_param("ii", $consultation_id, $doctor_id);
if ($stmt->execute() && $stmt->affected_rows > 0) {
    header("Location: add_prescription.php?id=$consultation_id&msg=Consultation+ended.+Add+prescription&type=success");
    exit();
} else {
    header("Location: doctor_dashboard.php?msg=Failed+to+end+consultation&type=error");
    exit();
}
?>
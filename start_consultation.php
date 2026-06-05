
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include "db.php";
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role'] ?? '') !== 'doctor') { header("Location: login.html"); exit(); }
$consultation_id = (int)($_GET['id'] ?? 0);
$doctor_id = (int)$_SESSION['user_id'];
if ($consultation_id <= 0) { header("Location: doctor_dashboard.php?msg=Invalid+Request&type=error"); exit(); }
// ⚠️ FIX: Zoom র‍্যান্ডম আইডি সাপোর্ট করে না। নিচে আপনার আসল মিটিং আইডি দিন
$zoom_id = "5735007045";   // 👈 আপনার Zoom Meeting ID
$zoom_pwd = "8dd1ohaQYJOenxETaaL4w2v0gCUH0g.1";      // 👈 Password (না থাকলে "" খালি রাখুন)
$stmt = $conn->prepare("UPDATE consultations SET status='ongoing', start_time=NOW(), zoom_meeting_id=?, zoom_meeting_pwd=? WHERE id=? AND doctor_id=?");
$stmt->bind_param("ssii", $zoom_id, $zoom_pwd, $consultation_id, $doctor_id);
if ($stmt->execute() && $stmt->affected_rows > 0) {
    header("Location: video_call.php?id=" . $consultation_id);
} else {
    header("Location: doctor_dashboard.php?msg=Error+starting+consultation&type=error");
}
exit();
?>
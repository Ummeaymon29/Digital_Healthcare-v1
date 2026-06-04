<?php
// 🔴 session_start() সবার আগে, কোনো স্পেস/লাইন আগে থাকবে না
if (session_status() === PHP_SESSION_NONE) session_start();
include "db.php";

// ✅ কেস-ইনসেনসিটিভ চেক (লোয়ারকেসে কম্পেয়ার)
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role'] ?? '') !== 'doctor') {
    header("Location: login.html?msg=Please+login+as+Doctor&type=error");
    exit(); // 🔥 জরুরি
}

if (!isset($_GET['id'])) {
    header("Location: doctor_dashboard.php?msg=Invalid+Request&type=error");
    exit();
}

$consultation_id = (int)$_GET['id'];
$doctor_id = (int)$_SESSION['user_id'];

// আপডেট কুয়েরি (শুধু এই ডক্টরের রিকোয়েস্ট)
$stmt = $conn->prepare("UPDATE consultations SET status='accepted' WHERE id=? AND doctor_id=?");
$stmt->bind_param("ii", $consultation_id, $doctor_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    header("Location: doctor_dashboard.php?msg=Request+accepted+successfully&type=success");
} else {
    header("Location: doctor_dashboard.php?msg=Failed+to+accept+request&type=error");
}
exit(); // 🔥 সর্বশেষ exit()
?>
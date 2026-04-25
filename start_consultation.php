<?php
session_start();
include "db.php";

if (!isset($_GET['id'])) {
    header("Location: doctor_dashboard.php?msg=Invalid+Request&type=error");
    exit();
}

$consultation_id = intval($_GET['id']);

// Verify doctor ownership (optional but recommended)
if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'Doctor') {
    $doctor_id = $_SESSION['user_id'];
    $query = "UPDATE consultations 
              SET status='ongoing', start_time=NOW() 
              WHERE id='$consultation_id' AND doctor_id='$doctor_id'";
} else {
    $query = "UPDATE consultations 
              SET status='ongoing', start_time=NOW() 
              WHERE id='$consultation_id'";
}

if(mysqli_query($conn, $query)){
    // ✅ ভিডিও কল পেজে রিডাইরেক্ট
    header("Location: video_call.php?id=" . $consultation_id);
    exit();
} else {
    header("Location: doctor_dashboard.php?msg=Error+starting+consultation&type=error");
    exit();
}
?>
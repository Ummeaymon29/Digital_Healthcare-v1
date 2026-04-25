<?php
session_start();
include "db.php";

if (!isset($_GET['id'])) {
    header("Location: doctor_dashboard.php?msg=Invalid+Request&type=error");
    exit();
}

$consultation_id = intval($_GET['id']);

$query = "UPDATE consultations 
          SET status='completed', end_time=NOW() 
          WHERE id='$consultation_id'";

if(mysqli_query($conn, $query)){
   
    header("Location: history.php?msg=Consultation+ended&type=success");
    exit();
} else {
    echo "Error ending consultation";
}
?>
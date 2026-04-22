<?php
include "db.php";

$consultation_id = $_GET['id'];

$query = "UPDATE consultations 
          SET status='completed', end_time=NOW() 
          WHERE id='$consultation_id'";

if(mysqli_query($conn, $query)){
    echo "Consultation Ended";
} else {
    echo "Error";
}
?>
<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Doctor') {
    header("Location: login.html");
    exit();
}

$doctor_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Dashboard</title>
</head>
<body>

<h1>Welcome Doctor</h1>

<a href="view_request.php?doctor_id=<?php echo $doctor_id; ?>">
View Requests
</a><br><br>

<h2>Your Consultations</h2>

<?php
$query = "SELECT * FROM consultations 
          WHERE doctor_id='$doctor_id' 
          AND (status='accepted' OR status='ongoing')";

$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        echo "Consultation ID: " . $row['id'] . "<br>";
        echo "Patient ID: " . $row['patient_id'] . "<br>";
        echo "Status: " . $row['status'] . "<br>";

        // 🔽 Buttons
        echo '<a href="start_consultation.php?id='.$row['id'].'">Start</a> | ';
        echo '<a href="video_call.php?id='.$row['id'].'">Join Call</a> | ';
        echo '<a href="end_consultation.php?id='.$row['id'].'">End</a>';

        echo "<hr>";
    }
} else {
    echo "No consultations found.";
}
?>

<br>
<a href="logout.php">Logout</a>

</body>
</html>
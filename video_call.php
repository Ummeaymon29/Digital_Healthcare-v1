<?php
session_start();
include "db.php";

if (!isset($_GET['id'])) {
    die("Invalid consultation ID");
}

$id = intval($_GET['id']);

// ডাটাবেজ থেকে তারিখ নাও (যদি start_time থাকে)
$res = mysqli_query($conn, "SELECT DATE(start_time) as cdate FROM consultations WHERE id=$id");
$date_row = mysqli_fetch_assoc($res);
$date = $date_row['cdate'] ? str_replace('-', '', $date_row['cdate']) : date('Ymd');

// ইউনিক রুম নাম: consultation_19_20250125
$room = "consultation_{$id}_{$date}";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Video Call - Consultation <?php echo $id; ?></title>
    <style>
        body{font-family:'Segoe UI',sans-serif;margin:0;padding:20px;background:#f8fafc;}
        .container{max-width:1000px;margin:0 auto;}
        .header{background:#fff;padding:15px;border-radius:8px;margin-bottom:15px;}
        iframe{width:100%;height:600px;border:none;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.1);}
        .btn{display:inline-block;padding:10px 20px;background:#dc2626;color:#fff;text-decoration:none;border-radius:6px;margin-top:15px;}
        .btn:hover{background:#b91c1c;}
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>📹 Consultation Room #<?php echo $id; ?></h2>
        <p>Room: <?php echo $room; ?></p>
    </div>
    
    <iframe src="https://meet.jit.si/<?php echo $room; ?>" 
            allow="camera; microphone; fullscreen; display-capture; autoplay"
            allowfullscreen></iframe>
    
    <br>
    <a href="end_consultation.php?id=<?php echo $id; ?>" class="btn">🔴 End Consultation</a>
    <a href="doctor_dashboard.php" style="margin-left:10px;">← Back</a>
</div>
</body>
</html>
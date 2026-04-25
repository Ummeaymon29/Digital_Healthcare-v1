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
<style>
body{font-family:'Segoe UI',sans-serif;background:#f8fafc;padding:30px;margin:0;}
.container{max-width:900px;margin:0 auto;}
h1{color:#1e293b;margin-bottom:25px;}
.card{background:#fff;padding:22px;border-radius:12px;margin-bottom:18px;box-shadow:0 2px 8px rgba(0,0,0,.06);}
.badge{padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;display:inline-block;text-transform:capitalize;}
.pending{background:#fef3c7;color:#92400e;}
.accepted{background:#dbeafe;color:#1e40af;}
.ongoing{background:#d1fae5;color:#065f46;}
.completed{background:#e5e7eb;color:#374151;}
.rejected{background:#fee2e2;color:#991b1b;}
.btn{padding:10px 18px;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;margin-right:8px;display:inline-block;transition:.3s;}
.btn-accept{background:#16a34a;color:#fff;}
.btn-accept:hover{background:#15803d;}
.btn-reject{background:#dc2626;color:#fff;}
.btn-reject:hover{background:#b91c1c;}
.btn-start{background:#2563eb;color:#fff;}
.btn-start:hover{background:#1d4ed8;}
.btn-end{background:#dc2626;color:#fff;}
.btn-end:hover{background:#b91c1c;}
.btn-view{background:#64748b;color:#fff;}
.btn-view:hover{background:#475569;}
.info{margin-bottom:12px;color:#64748b;font-size:14px;}
.actions{margin-top:15px;}
.logout{color:#dc2626;text-decoration:none;font-size:14px;float:right;}
.logout:hover{text-decoration:underline;}
</style>
</head>
<body>
<div class="container">
<h1>👨‍️ Doctor Dashboard <a href="logout.php" class="logout">Logout</a></h1>

<a href="view_request.php?doctor_id=<?php echo $doctor_id; ?>" class="btn btn-view">📩 View All Requests</a>
<br><br>

<h2>Your Consultations</h2>
<?php
$query = "SELECT c.*, u.name as patient_name FROM consultations c 
          JOIN users u ON c.patient_id=u.id 
          WHERE c.doctor_id='$doctor_id' 
          ORDER BY c.id DESC";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        echo "<div class='card'>";
        echo "<div style='display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;'>";
        echo "<div>";
        echo "<h3 style='margin:0 0 8px;'>👤 " . htmlspecialchars($row['patient_name']) . "</h3>";
        echo "<div class='info'>📝 " . htmlspecialchars($row['message']) . "</div>";
        if(!empty($row['date']) || !empty($row['time'])){
            echo "<div class='info'>📅 " . htmlspecialchars($row['date'] ?: 'N/A') . " | ⏰ " . htmlspecialchars($row['time'] ?: 'N/A') . "</div>";
        }
        echo "<span class='badge " . strtolower($row['status']) . "'>" . ucfirst($row['status']) . "</span>";
        echo "</div>";
        
        echo "<div class='actions'>";
        
        if($row['status'] == 'pending'){
            echo '<a href="accept.php?id='.$row['id'].'" class="btn btn-accept">✅ Accept</a>';
            echo '<a href="reject.php?id='.$row['id'].'" class="btn btn-reject">❌ Reject</a>';
        }
        elseif($row['status'] == 'accepted'){
            echo '<a href="start_consultation.php?id='.$row['id'].'" class="btn btn-start">▶️ Start</a>';
            echo '<a href="consultation.php?id='.$row['id'].'" class="btn btn-view">👁️ View</a>';
        }
        elseif($row['status'] == 'ongoing'){
            echo '<a href="video_call.php?id='.$row['id'].'" class="btn btn-start">📺 Join</a>';
            echo '<a href="end_consultation.php?id='.$row['id'].'" class="btn btn-end">🔴 End</a>';
        }
        elseif($row['status'] == 'completed'){
            echo '<a href="consultation.php?id='.$row['id'].'" class="btn btn-view">📜 View Details</a>';
        }
        else{
            echo '<span style="color:#991b1b;font-weight:600;">Rejected</span>';
        }
        
        echo "</div></div></div>";
    }
} else {
    echo "<div class='card'><p>No consultations found.</p></div>";
}
?>
</div>
</body>
</html>
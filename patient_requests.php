<?php
session_start();
include "db.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    header("Location: login.html"); exit();
}

$patient_id = (int)$_SESSION['user_id'];
$stmt = $conn->prepare("SELECT c.id, u.name as doctor_name, c.date, c.time, c.message, c.status 
                        FROM consultations c 
                        JOIN users u ON c.doctor_id = u.id 
                        WHERE c.patient_id = ? 
                        ORDER BY c.id DESC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Requests</title>
<style>
:root{--primary:#2563eb;--bg:#f8fafc;--card:#fff;--text:#1e293b;}
body{font-family:'Segoe UI',sans-serif;background:var(--bg);margin:0;padding:30px;color:var(--text);}
.container{max-width:850px;margin:0 auto;}
h1{margin:0 0 20px;font-size:24px;}
.req-card{background:var(--card);padding:20px;border-radius:12px;margin-bottom:15px;box-shadow:0 2px 8px rgba(0,0,0,.05);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;}
.info h3{margin:0 0 5px;font-size:18px;color:var(--primary);}
.info p{margin:0;color:#64748b;font-size:14px;}
.badge{padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;text-transform:capitalize;}
.pending{background:#fef3c7;color:#92400e;} .accepted{background:#dbeafe;color:#1e40af;}
.ongoing{background:#d1fae5;color:#065f46;} .completed{background:#e5e7eb;color:#374151;}
.rejected{background:#fee2e2;color:#991b1b;}
.btn{padding:8px 14px;border-radius:6px;text-decoration:none;font-size:13px;font-weight:500;color:#fff;}
.btn-join{background:var(--primary);} .btn-view{background:#64748b;}
.back{color:#64748b;text-decoration:none;font-size:14px;display:inline-block;margin-bottom:15px;}
</style>
</head>
<body>
<div class="container">
<a href="patient_dashboard.php" class="back">← Back to Dashboard</a>
<h1>📄 My Consultation Requests</h1>
<?php while($row=$result->fetch_assoc()): ?>
<div class="req-card">
<div class="info">
<h3>👨‍⚕️ <?=htmlspecialchars($row['doctor_name'])?></h3>
<p>📅 <?=htmlspecialchars($row['date'])?> | ⏰ <?=htmlspecialchars($row['time'])?></p>
<p>📝 <?=htmlspecialchars($row['message'])?:'No message'?></p>
</div>
<div style="text-align:right;">
<span class="badge <?=strtolower($row['status'])?>"><?=ucfirst($row['status'])?></span><br><br>
<?php if($row['status']=='accepted'): ?>
<a href="consultation.php?id=<?=$row['id']?>" class="btn btn-join">🎥 Join/Start</a>
<?php elseif($row['status']=='ongoing'): ?>
<a href="consultation.php?id=<?=$row['id']?>" class="btn btn-join">📺 In Session</a>
<?php elseif($row['status']=='completed'): ?>
<a href="consultation.php?id=<?=$row['id']?>" class="btn btn-view">👁️ View Details</a>
<?php endif; ?>
</div>
</div>
<?php endwhile; ?>
</div>
</body>
</html>
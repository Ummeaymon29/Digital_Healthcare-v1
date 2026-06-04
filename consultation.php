<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include "db.php";
if (!isset($_SESSION['user_id'])) { header("Location: login.html"); exit(); }
if (!isset($_GET['id'])) die("Invalid Consultation ID");
$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT c.*, d.name as doctor_name, p.name as patient_name FROM consultations c JOIN users d ON c.doctor_id=d.id JOIN users p ON c.patient_id=p.id WHERE c.id=? LIMIT 1");
$stmt->bind_param("i", $id); $stmt->execute(); $row = $stmt->get_result()->fetch_assoc();
if (!$row) die("Consultation not found.");
$backLink = (strtolower($_SESSION['role']) === 'doctor') ? 'doctor_dashboard.php' : 'patient_dashboard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Consultation Details</title>
<style>
:root{--primary:#2563eb;--bg:#f8fafc;--card:#fff;}
body{font-family:'Segoe UI',sans-serif;background:var(--bg);margin:0;padding:25px;color:#1e293b;}
.container{max-width:800px;margin:0 auto;}
.card{background:var(--card);padding:25px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.06);margin-bottom:20px;}
h2{margin:0 0 20px;color:var(--primary);}
.info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;margin-bottom:20px;}
.info-box{padding:15px;background:#f1f5f9;border-radius:8px;}
.info-box label{display:block;font-size:12px;color:#64748b;margin-bottom:4px;}
.info-box strong{font-size:15px;color:#1e293b;}
.badge{padding:6px 12px;border-radius:15px;font-size:13px;font-weight:600;text-transform:capitalize;}
.pending{background:#fef3c7;color:#92400e;}.accepted{background:#dbeafe;color:#1e40af;}
.ongoing{background:#d1fae5;color:#065f46;}.completed{background:#e5e7eb;color:#374151;}
.rejected{background:#fee2e2;color:#991b1b;}
.btn{display:inline-block;padding:10px 18px;background:var(--primary);color:#fff;text-decoration:none;border-radius:8px;font-weight:500;margin-top:15px;}
.btn:hover{background:#1d4ed8;}
.back{color:#64748b;text-decoration:none;font-size:14px;display:inline-block;margin-bottom:20px;}
</style></head>
<body>
<div class="container">
<a href="<?=$backLink?>" class="back">← Back to Dashboard</a>
<div class="card">
<h2>📋 Consultation #<?=$row['id']?></h2>
<span class="badge <?=strtolower($row['status'])?>"><?=ucfirst($row['status'])?></span>
<div class="info-grid" style="margin-top:20px;">
<div class="info-box"><label>👨‍⚕️ Doctor</label><strong><?=htmlspecialchars($row['doctor_name'])?></strong></div>
<div class="info-box"><label>👤 Patient</label><strong><?=htmlspecialchars($row['patient_name'])?></strong></div>
<div class="info-box"><label>📅 Date & Time</label><strong><?=htmlspecialchars($row['date']?:'N/A')?> | <?=htmlspecialchars($row['time']?:'N/A')?></strong></div>
<div class="info-box"><label>📝 Message</label><strong><?=htmlspecialchars($row['message']?:'N/A')?></strong></div>
<?php if(!empty($row['start_time'])): ?>
<div class="info-box"><label>▶️ Started At</label><strong><?=htmlspecialchars($row['start_time'])?></strong></div>
<?php endif; ?>
<?php if(!empty($row['end_time'])): ?>
<div class="info-box"><label>🔴 Ended At</label><strong><?=htmlspecialchars($row['end_time'])?></strong></div>
<?php endif; ?>
</div>
<?php if($row['status']=='ongoing' && $_SESSION['role']=='Doctor'): ?>
<a href="video_call.php?id=<?=$row['id']?>" class="btn" style="background:#10b981;">📺 Join Zoom Call</a>
<?php endif; ?>
</div>
</div></body></html>
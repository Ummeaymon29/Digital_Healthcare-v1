<?php
session_start(); include "db.php";
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role'] ?? '') !== 'doctor') { header("Location: login.html?msg=Please+login+as+Doctor&type=error"); exit(); }
$doctor_id = (int)$_SESSION['user_id'];
$msg = $_GET['msg'] ?? ''; $type = $_GET['type'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Doctor Dashboard</title>
<style>:root{--primary:#2563eb;--bg:#f8fafc;--card:#fff;--text:#1e293b;--border:#e2e8f0;}body{font-family:'Segoe UI',sans-serif;background:var(--bg);margin:0;padding:25px;color:var(--text);}.container{max-width:1000px;margin:0 auto;}header{display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;flex-wrap:wrap;gap:10px;}h1{margin:0;color:var(--primary);font-size:26px;}.badge{padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;text-transform:capitalize;}.pending{background:#fef3c7;color:#92400e;}.accepted{background:#dbeafe;color:#1e40af;}.ongoing{background:#d1fae5;color:#065f46;}.completed{background:#e5e7eb;color:#374151;}.rejected{background:#fee2e2;color:#991b1b;}.card{background:var(--card);padding:20px;border-radius:12px;margin-bottom:16px;box-shadow:0 3px 10px rgba(0,0,0,.06);border:1px solid var(--border);}.flex{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;}.info h3{margin:0 0 6px;font-size:18px;color:var(--primary);}.info p{margin:4px 0;color:#64748b;font-size:14px;}.btn{padding:8px 14px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;color:#fff;display:inline-block;transition:.2s;}.btn-accept{background:#10b981;}.btn-reject{background:#ef4444;}.btn-start{background:#2563eb;}.btn-end{background:#dc2626;}.btn-view{background:#64748b;}.btn:hover{opacity:.9;transform:translateY(-1px);}.logout{color:#dc2626;text-decoration:none;font-weight:500;}.alert{padding:12px;border-radius:8px;margin-bottom:15px;font-size:14px;}.success{background:#dcfce7;color:#166534;}.error{background:#fee2e2;color:#991b1b;}</style></head>
<body><div class="container">
<header><h1>👨‍⚕️ Doctor Dashboard</h1><div><a href="doctor_dashboard.php" class="btn btn-view" style="margin-right:8px;">🔄 Refresh</a><a href="logout.php" class="logout">Logout</a></div></header>
<?php if($msg): ?><div class="alert <?=$type?>"><?=$msg?></div><?php endif; ?>
<?php
$res = mysqli_query($conn, "SELECT c.*, u.name as patient_name FROM consultations c JOIN users u ON c.patient_id=u.id WHERE c.doctor_id=$doctor_id ORDER BY c.id DESC");
while($row = mysqli_fetch_assoc($res)):
?>
<div class="card"><div class="flex">
<div class="info"><h3>👤 <?=htmlspecialchars($row['patient_name'])?></h3><p>📝 <?=htmlspecialchars($row['message']?:'No details')?></p><p>📅 <?=htmlspecialchars($row['date']?:'N/A')?> | ⏰ <?=htmlspecialchars($row['time']?:'N/A')?></p><span class="badge <?=strtolower($row['status'])?>"><?=ucfirst($row['status'])?></span></div>
<div>
<?php if($row['status']=='pending'): ?>
<a href="accept.php?id=<?=$row['id']?>" class="btn btn-accept">✅ Accept</a>
<a href="reject.php?id=<?=$row['id']?>" class="btn btn-reject">❌ Reject</a>
<?php elseif($row['status']=='accepted'): ?>
<a href="start_consultation.php?id=<?=$row['id']?>" class="btn btn-start">▶️ Start</a>
<?php elseif($row['status']=='ongoing'): ?>
<a href="video_call.php?id=<?=$row['id']?>" class="btn btn-start">📺 Join</a>
<a href="end_consultation.php?id=<?=$row['id']?>" class="btn btn-end">🔴 End</a>
<?php elseif($row['status']=='completed'): ?>
<a href="consultation.php?id=<?=$row['id']?>" class="btn btn-view">📜 View</a>
<a href="add_prescription.php?id=<?=$row['id']?>" class="btn btn-view">💊 Prescribe</a>
<?php else: ?>
<span style="color:#991b1b;font-weight:600;">Rejected</span>
<?php endif; ?>
</div></div></div>
<?php endwhile; ?>
</div></body></html>
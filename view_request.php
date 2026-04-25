<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: login.html"); exit();
}

$doctor_id = (int)$_SESSION['user_id'];
$sql = "SELECT c.*, u.name as patient_name 
        FROM consultations c 
        JOIN users u ON c.patient_id = u.id 
        WHERE c.doctor_id = '$doctor_id' 
        ORDER BY c.id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Consultation Requests</title>
<style>
:root{--primary:#2563eb;--success:#16a34a;--danger:#dc2626;--bg:#f8fafc;--card:#fff;}
body{font-family:'Segoe UI',sans-serif;background:var(--bg);margin:0;padding:30px;color:#1e293b;}
.container{max-width:900px;margin:0 auto;}
h1{margin:0 0 25px;font-size:26px;}
.req-card{background:var(--card);padding:22px;border-radius:12px;margin-bottom:18px;box-shadow:0 3px 10px rgba(0,0,0,.08);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:15px;}
.info h3{margin:0 0 8px;font-size:19px;color:var(--primary);}
.info p{margin:0 0 6px;color:#64748b;font-size:14px;}
.badge{padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;text-transform:capitalize;display:inline-block;}
.pending{background:#fef3c7;color:#92400e;}
.accepted{background:#dbeafe;color:#1e40af;}
.ongoing{background:#d1fae5;color:#065f46;}
.completed{background:#e5e7eb;color:#374151;}
.rejected{background:#fee2e2;color:#991b1b;}
.actions{display:flex;gap:10px;flex-wrap:wrap;}
.btn{padding:10px 18px;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;transition:.3s;}
.btn-accept{background:var(--success);color:#fff;}
.btn-accept:hover{background:#15803d;}
.btn-reject{background:var(--danger);color:#fff;}
.btn-reject:hover{background:#b91c1c;}
.btn-view{background:#64748b;color:#fff;}
.btn-view:hover{background:#475569;}
.back{color:#64748b;text-decoration:none;font-size:14px;display:inline-block;margin-bottom:20px;}
.back:hover{color:var(--primary);}
.no-req{background:var(--card);padding:40px;text-align:center;border-radius:12px;color:#64748b;}
</style>
</head>
<body>
<div class="container">
<a href="doctor_dashboard.php" class="back">← Back to Dashboard</a>
<h1>📩 Consultation Requests</h1>

<?php if(mysqli_num_rows($result) > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
    <div class="req-card">
        <div class="info">
            <h3>👤 <?=htmlspecialchars($row['patient_name'])?></h3>
            <p>📝 <?=htmlspecialchars($row['message'] ?: 'No message')?></p>
            <?php if(!empty($row['date']) || !empty($row['time'])): ?>
            <p>📅 <?=htmlspecialchars($row['date'] ?: 'N/A')?> | ⏰ <?=htmlspecialchars($row['time'] ?: 'N/A')?></p>
            <?php endif; ?>
            <p> Status: <span class="badge <?=strtolower($row['status'])?>"><?=ucfirst($row['status'])?></span></p>
        </div>
        <div class="actions">
            <?php if($row['status'] == 'pending'): ?>
                <a href="accept.php?id=<?=$row['id']?>" class="btn btn-accept">✅ Accept</a>
                <a href="reject.php?id=<?=$row['id']?>" class="btn btn-reject">❌ Reject</a>
            <?php elseif($row['status'] == 'accepted'): ?>
                <a href="start_consultation.php?id=<?=$row['id']?>" class="btn btn-accept">▶️ Start Call</a>
                <a href="consultation.php?id=<?=$row['id']?>" class="btn btn-view">👁️ View</a>
            <?php elseif($row['status'] == 'ongoing'): ?>
                <a href="video_call.php?id=<?=$row['id']?>" class="btn btn-accept">📺 Join Call</a>
                <a href="end_consultation.php?id=<?=$row['id']?>" class="btn btn-reject">🔴 End</a>
            <?php elseif($row['status'] == 'completed'): ?>
                <a href="consultation.php?id=<?=$row['id']?>" class="btn btn-view">📜 View Details</a>
            <?php else: ?>
                <span style="color:#991b1b;font-weight:600;">Rejected</span>
            <?php endif; ?>
        </div>
    </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="no-req">
        <h2>📭 No Requests Found</h2>
        <p>You don't have any consultation requests yet.</p>
    </div>
<?php endif; ?>

</div>
</body>
</html>
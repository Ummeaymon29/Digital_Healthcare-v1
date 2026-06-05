<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include "db.php";

// ✅ কেস-ইনসেনসিটিভ ও সেফ চেক
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role'] ?? '') !== 'patient') {
    header("Location: login.html?msg=Please+login+first&type=error");
    exit();
}

$patient_id = (int)$_SESSION['user_id'];
$msg = $_GET['msg'] ?? '';
$type = $_GET['type'] ?? '';

// রিকোয়েস্ট ফেচ (Zoom ID সহ)
$stmt = $conn->prepare("SELECT c.id, u.name as doctor_name, c.date, c.time, c.message, c.status, c.zoom_meeting_id, c.zoom_meeting_pwd
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
.btn-join{background:var(--primary);} .btn-join:hover{background:#1d4ed8;}
.btn-view{background:#64748b;} .btn-view:hover{background:#475569;}
.back{color:#64748b;text-decoration:none;font-size:14px;display:inline-block;margin-bottom:15px;}
.alert{padding:12px;border-radius:8px;margin-bottom:15px;font-size:14px;}
.success{background:#dcfce7;color:#166534;} .error{background:#fee2e2;color:#991b1b;}
</style>
</head>
<body>
<div class="container">
<a href="patient_dashboard.php" class="back">← Back to Dashboard</a>
<h1>📄 My Consultation Requests</h1>
<?php if($msg): ?><div class="alert <?=$type?>"><?=$msg?></div><?php endif; ?>

<?php if(mysqli_num_rows($result) == 0): ?>
<p style="color:#64748b;text-align:center;padding:40px;">No consultation requests found.</p>
<?php else: ?>
<?php while($row=$result->fetch_assoc()): 
    $zoom_url = '';
    if (!empty($row['zoom_meeting_id'])) {
        $zoom_url = "https://zoom.us/j/{$row['zoom_meeting_id']}" . 
                    (!empty($row['zoom_meeting_pwd']) ? "?pwd={$row['zoom_meeting_pwd']}" : "");
    }
?>
<div class="req-card">
    <div class="info">
        <h3>👨‍⚕️ <?=htmlspecialchars($row['doctor_name'])?></h3>
        <p>📅 <?=htmlspecialchars($row['date'])?> | ⏰ <?=htmlspecialchars($row['time'])?></p>
        <p>📝 <?=htmlspecialchars($row['message']?:'No message')?></p>
    </div>
    <div style="text-align:right;">
        <span class="badge <?=strtolower($row['status'])?>"><?=ucfirst($row['status'])?></span><br><br>
        <?php if($row['status']=='accepted'): ?>
            <span style="color:#f59e0b;font-size:13px;">⏳ Doctor accepted. Waiting to start...</span>
        <?php elseif($row['status']=='ongoing' && $zoom_url): ?>
            <a href="<?=$zoom_url?>" target="_blank" class="btn btn-join">📺 Join Zoom Call</a>
        <?php elseif($row['status']=='completed'): ?>
            <a href="consultation.php?id=<?=$row['id']?>" class="btn btn-view">👁️ View Details</a>
            <a href="my_prescriptions.php" class="btn btn-view" style="background:#10b981;margin-top:5px;">💊 Prescriptions</a>
        <?php elseif($row['status']=='pending'): ?>
            <span style="color:#9ca3af;font-size:13px;">⏳ Pending Approval</span>
        <?php else: ?>
            <span style="color:#ef4444;font-size:13px;">❌ Rejected</span>
        <?php endif; ?>
    </div>
</div>
<?php endwhile; ?>
<?php endif; ?>
</div>
</body>
</html>
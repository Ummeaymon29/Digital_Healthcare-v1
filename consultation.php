<?php
include "db.php";
session_start();

// Safety check for ID
if (!isset($_GET['id'])) {
    die("Invalid Consultation ID");
}
$id = intval($_GET['id']);

$result = mysqli_query($conn, "SELECT c.*, d.name as doctor_name, p.name as patient_name FROM consultations c 
                               JOIN users d ON c.doctor_id=d.id 
                               JOIN users p ON c.patient_id=p.id 
                               WHERE c.id='$id' LIMIT 1");
$row = mysqli_fetch_assoc($result);

if(!$row){ die("Consultation not found."); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Consultation Room</title>
<style>
:root{--primary:#3498db;--success:#2ecc71;--warning:#f39c12;--danger:#e74c3c;--bg:#f0f2f5;--text:#2c3e50;}
body{font-family:'Segoe UI',sans-serif;background:var(--bg);margin:0;padding:20px;min-height:100vh;}
.container{max-width:900px;margin:0 auto;}
.header{background:#fff;padding:20px;border-radius:12px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;box-shadow:0 2px 8px rgba(0,0,0,.05);}
h2{margin:0;color:var(--text);font-size:20px;}
.badge{padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;text-transform:uppercase;}
.badge-accepted{background:#e8f4fc;color:var(--primary);}
.badge-ongoing{background:#e8f8e8;color:var(--success);}
.badge-completed{background:#f0f0f0;color:#777;}

.info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;margin-bottom:20px;}
.info-card{background:#fff;padding:15px;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,.04);}
.info-card span{display:block;font-size:13px;color:#777;margin-bottom:4px;}
.info-card strong{font-size:16px;color:var(--text);}

.video-wrapper{position:relative;width:100%;padding-bottom:56.25%;height:0;background:#000;border-radius:12px;overflow:hidden;margin-bottom:20px;box-shadow:0 4px 12px rgba(0,0,0,.1);}
.video-wrapper iframe{position:absolute;top:0;left:0;width:100%;height:100%;border:none;}

.controls{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;}
.btn{padding:12px 24px;border:none;border-radius:8px;cursor:pointer;font-size:14px;font-weight:600;transition:.3s;text-decoration:none;}
.btn-start{background:var(--success);color:#fff;}
.btn-end{background:var(--danger);color:#fff;}
.btn-back{background:#ecf0f1;color:#34495e;}
.btn:hover{opacity:.9;transform:translateY(-1px);}
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>📹 Consultation #<?php echo $row['id']; ?></h2>
        <span class="badge badge-<?php echo strtolower($row['status']); ?>"><?php echo ucfirst($row['status']); ?></span>
    </div>

    <div class="info-grid">
        <div class="info-card">
            <span>👨‍⚕️ Doctor</span>
            <strong><?php echo htmlspecialchars($row['doctor_name']); ?></strong>
        </div>
        <div class="info-card">
            <span>👤 Patient</span>
            <strong><?php echo htmlspecialchars($row['patient_name']); ?></strong>
        </div>
        <div class="info-card">
            <span>📅 Date & Time</span>
            <strong><?php echo $row['date']; ?> | <?php echo $row['time']; ?></strong>
        </div>
        <div class="info-card">
            <span>📝 Symptoms</span>
            <strong><?php echo htmlspecialchars($row['description'] ?? 'N/A'); ?></strong>
        </div>
    </div>

    <?php if($row['status'] == 'accepted'): ?>
    <div style="text-align:center;margin-bottom:20px;">
        <a href="start_consultation.php?id=<?php echo $id; ?>" class="btn btn-start">🎥 Start Consultation Now</a>
    </div>
    <?php endif; ?>

    <div class="video-wrapper">
        <iframe src="https://meet.jit.si/consultation_<?php echo $id; ?>_<?php echo date('Ymd'); ?>" 
                allow="camera; microphone; fullscreen; display-capture; autoplay"
                allowfullscreen></iframe>
    </div>

    <div class="controls">
        <?php if($row['status'] == 'ongoing'): ?>
        <a href="end_consultation.php?id=<?php echo $id; ?>" class="btn btn-end">🔴 End Consultation</a>
        <?php endif; ?>
        <a href="dashboard.php" class="btn btn-back">← Back to Dashboard</a>
    </div>
</div>
</body>
</html>
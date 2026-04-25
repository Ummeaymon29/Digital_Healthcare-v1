<?php
session_start();
include "db.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    header("Location: login.html"); exit();
}
$patient_id = (int)$_SESSION['user_id'];

// Fetch doctors
$docStmt = $conn->prepare("SELECT id, name FROM users WHERE role='Doctor' ORDER BY name");
$docStmt->execute();
$doctors = $docStmt->get_result();

// Handle success/error messages
$msg = $_GET['msg'] ?? '';
$type = $_GET['type'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Dashboard</title>
<style>
:root{--primary:#2563eb;--bg:#f8fafc;--text:#1e293b;--card:#fff;--border:#e2e8f0;}
body{font-family:'Segoe UI',sans-serif;background:var(--bg);margin:0;padding:30px;color:var(--text);}
.container{max-width:800px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:25px;}
@media(max-width:768px){.container{grid-template-columns:1fr;}}
.card{background:var(--card);padding:25px;border-radius:14px;box-shadow:0 4px 12px rgba(0,0,0,.06);}
h2{margin:0 0 20px;font-size:20px;color:var(--primary);}
.form-group{margin-bottom:15px;}
label{display:block;margin-bottom:6px;font-weight:500;font-size:14px;}
input,select,textarea{width:100%;padding:12px;border:1px solid var(--border);border-radius:8px;font-size:14px;box-sizing:border-box;}
textarea{resize:vertical;min-height:90px;}
.btn{width:100%;padding:12px;background:var(--primary);color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;margin-top:10px;}
.btn:hover{opacity:.9;}
.alert{padding:12px;border-radius:8px;margin-bottom:15px;font-size:14px;}
.success{background:#dcfce7;color:#166534;} .error{background:#fee2e2;color:#991b1b;}
.link{color:var(--primary);text-decoration:none;font-weight:500;}
</style></head>
<body>
<div class="container">
<div class="card">
<h2>🩺 Request Consultation</h2>
<?php if($msg): ?><div class="alert <?=$type?>"><?=htmlspecialchars($msg)?></div><?php endif; ?>
<form action="send_request.php" method="POST">
<div class="form-group"><label>Select Doctor</label>
<select name="doctor_id" required>
<option value="" disabled selected>Choose a doctor...</option>
<?php while($d=$doctors->fetch_assoc()): ?>
<option value="<?=$d['id']?>"><?=htmlspecialchars($d['name'])?></option>
<?php endwhile; ?>
</select></div>
<div class="form-group"><label>Date</label><input type="date" name="date" required></div>
<div class="form-group"><label>Time</label><input type="time" name="time" required></div>
<div class="form-group"><label>Symptoms / Message</label><textarea name="message" required></textarea></div>
<button type="submit" class="btn">📩 Send Request</button>
</form>
</div>
<div class="card">
<h2>📋 Quick Actions</h2>
<p style="margin:15px 0;color:#64748b;">Manage your appointments & health records.</p>
<a href="patient_requests.php" class="link">📄 View My Requests</a><br><br>
<a href="view_doctors.php" class="link">👨‍⚕️ Browse All Doctors</a><br><br>
<a href="history.php" class="link">📜 Consultation History</a><br><br>
<a href="logout.php" class="link" style="color:#ef4444;">🚪 Logout</a>
</div>
</div>
</body></html>
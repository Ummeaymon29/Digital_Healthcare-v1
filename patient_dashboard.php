<?php
// 🔴 session_start() আগে কোনো আউটপুট/স্পেস থাকবে না
if (session_status() === PHP_SESSION_NONE) session_start();
include "db.php";

// ✅ কেস-ইনসেনসিটিভ ও সেফ রোল চেক
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role'] ?? '') !== 'patient') {
    header("Location: login.html?msg=Please+login+first&type=error");
    exit();
}

// ✅ মেসেজ ভেরিয়েবল ইনিশিয়ালাইজ
$msg = $_GET['msg'] ?? '';
$type = $_GET['type'] ?? '';

// ✅ ডক্টর লিস্ট ফেচ (এটা মিসিং ছিল!)
$docStmt = $conn->prepare("SELECT id, name FROM users WHERE role='Doctor' ORDER BY name");
$docStmt->execute();
$doctors = $docStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Dashboard</title>
<style>
:root{--primary:#2563eb;--bg:#f8fafc;--card:#fff;--text:#1e293b;--border:#e2e8f0;}
body{font-family:'Segoe UI',sans-serif;background:var(--bg);margin:0;padding:30px;color:var(--text);}
.container{max-width:900px;margin:0 auto;display:grid;grid-template-columns:1.2fr 1fr;gap:25px;}
@media(max-width:768px){.container{grid-template-columns:1fr;}}
.card{background:var(--card);padding:25px;border-radius:14px;box-shadow:0 4px 12px rgba(0,0,0,.06);border:1px solid var(--border);}
h2{margin:0 0 15px;color:var(--primary);font-size:20px;}
.form-group{margin-bottom:14px;}
label{display:block;margin-bottom:6px;font-weight:500;font-size:14px;}
input,select,textarea{width:100%;padding:11px;border:1px solid var(--border);border-radius:8px;font-size:14px;box-sizing:border-box;transition:.2s;}
input:focus,select:focus,textarea:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(37,99,235,.1);}
textarea{resize:vertical;min-height:80px;}
.btn{width:100%;padding:12px;background:var(--primary);color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;margin-top:10px;transition:.2s;}
.btn:hover{background:#1d4ed8;}
.alert{padding:12px;border-radius:8px;margin-bottom:15px;font-size:14px;}
.success{background:#dcfce7;color:#166534;} .error{background:#fee2e2;color:#991b1b;}
.link{color:var(--primary);text-decoration:none;font-weight:500;display:inline-block;margin:8px 0;transition:.2s;}
.link:hover{color:#1e40af;text-decoration:underline;}
header{margin-bottom:25px;display:flex;justify-content:space-between;align-items:center;}
.logout{color:#dc2626;text-decoration:none;font-weight:500;}
</style></head>
<body>
<div class="container" style="grid-template-columns:1fr; margin-bottom:25px;">
    <header>
        <h1>👤 Welcome, <?=htmlspecialchars($_SESSION['name'] ?? 'Patient')?></h1>
        <a href="logout.php" class="logout">🚪 Logout</a>
    </header>
</div>

<div class="container">
<div class="card">
    <h2>🩺 Request Consultation</h2>
    <?php if($msg): ?><div class="alert <?=$type?>"><?=$msg?></div><?php endif; ?>
    <form action="send_request.php" method="POST">
        <div class="form-group">
            <label>Select Doctor</label>
            <select name="doctor_id" required>
                <option value="" disabled selected>Choose a doctor...</option>
                <?php while($d = $doctors->fetch_assoc()): ?>
                <option value="<?=$d['id']?>"><?=htmlspecialchars($d['name'])?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group"><label>Date</label><input type="date" name="date" required></div>
        <div class="form-group"><label>Time</label><input type="time" name="time" required></div>
        <div class="form-group"><label>Symptoms / Message</label><textarea name="message" placeholder="- Fever for 2 days&#10;- Headache&#10;- Write other symptoms..." required></textarea></div>
        <button type="submit" class="btn">📩 Send Request</button>
    </form>
</div>

<div class="card">
    <h2>📋 Quick Actions</h2>
    <a href="patient_requests.php" class="link">📄 View My Requests</a>
    <a href="view_doctors.php" class="link">👨‍⚕️ Browse Doctors</a>
    <a href="history.php" class="link">📜 Consultation History</a>
    <a href="my_prescriptions.php" class="link">💊 My Prescriptions</a>
    <a href="medicine_search.php" class="link">💊 Medicine Database</a>
    <a href="my_reminders.php" class="link">⏰ My Reminders</a>
    <a href="add_reminder.php" class="link" style="color:#10b981;">➕ Set Reminder</a>
</div>
</div>
</body></html>
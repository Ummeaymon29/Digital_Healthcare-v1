<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    header("Location: login.html"); exit();
}
$stmt = $conn->prepare("SELECT id, name, phone FROM users WHERE role='Doctor' ORDER BY name");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Doctor Directory</title>
<style>
:root{--primary:#2563eb;--bg:#f8fafc;--card:#fff;}
body{font-family:'Segoe UI',sans-serif;background:var(--bg);padding:30px;color:#1e293b;}
.container{max-width:900px;margin:0 auto;}
h1{margin-bottom:25px;}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;}
.doc-card{background:var(--card);padding:20px;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.06);}
.doc-card h3{margin:0 0 8px;color:var(--primary);}
.doc-card p{margin:0 0 12px;color:#64748b;font-size:14px;}
.back{color:#64748b;text-decoration:none;font-size:14px;display:inline-block;margin-bottom:15px;}
</style></head>
<body>
<div class="container">
<a href="patient_dashboard.php" class="back">← Back to Dashboard</a>
<h1>👨‍⚕️ Available Doctors</h1>
<div class="grid">
<?php while($row=$result->fetch_assoc()): ?>
<div class="doc-card">
<h3><?=htmlspecialchars($row['name'])?></h3>
<p>📞 <?=htmlspecialchars($row['phone']?:'Not available')?></p>
<p>📅 Book via Patient Dashboard</p>
</div>
<?php endwhile; ?>
</div>
</div>
</body></html>
<?php
session_start();
include "db.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); exit();
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM medicines WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$medicine = $result->fetch_assoc();

if (!$medicine) {
    header("Location: medicine_search.php?msg=Medicine+not+found&type=error");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Medicine Details</title>
<style>
:root{--primary:#2563eb;--bg:#f8fafc;--card:#fff;--success:#10b981;--warning:#f59e0b;}
body{font-family:'Segoe UI',sans-serif;background:var(--bg);padding:30px;margin:0;color:#1e293b;}
.container{max-width:800px;margin:0 auto;}
.card{background:var(--card);padding:30px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.06);margin-bottom:20px;}
h1{margin:0 0 20px;color:var(--primary);}
.info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin-top:20px;}
.info-box{padding:15px;background:#f8fafc;border-radius:8px;border-left:4px solid var(--primary);}
.info-box label{display:block;font-size:13px;color:#64748b;margin-bottom:5px;}
.info-box strong{font-size:16px;color:#1e293b;}
.back{color:#64748b;text-decoration:none;font-size:14px;display:inline-block;margin-bottom:20px;}
.btn{display:inline-block;padding:12px 24px;background:var(--primary);color:#fff;text-decoration:none;border-radius:8px;font-weight:600;margin-top:15px;}
</style>
</head>
<body>
<div class="container">
    <a href="medicine_search.php" class="back">← Back to Search</a>
    <h1>💊 Medicine Details</h1>
    
    <div class="card">
        <h2><?=htmlspecialchars($medicine['name'])?></h2>
        
        <div class="info-grid">
            <div class="info-box">
                <label>Category</label>
                <strong><?=htmlspecialchars($medicine['category'] ?? 'N/A')?></strong>
            </div>
            <div class="info-box">
                <label>Price</label>
                <strong>৳<?=number_format($medicine['price'], 2)?></strong>
            </div>
              <div class="description">
        <h3>📋 Description</h3>
        <p><?=nl2br(htmlspecialchars($medicine['description']))?></p>
    </div>
    <div class="side-effects">
        <h3>⚠️ Side Effects</h3>
        <p><?=nl2br(htmlspecialchars($medicine['side_effects']))?></p>
    </div>
          <div class="description" style="background:#dbeafe;border-left-color:#3b82f6;">
        <h3>💊 Dosage Instructions</h3>
        <p><?=nl2br(htmlspecialchars($medicine['dosage_instructions']))?></p>
    </div>
       <div class="side-effects" style="background:#fef3c7;border-left-color:#f59e0b;">
        <h3>⚡ Warnings</h3>
        <p><?=nl2br(htmlspecialchars($medicine['warnings']))?></p>
    </div>
        </div>
        
        <a href="medicine_search.php" class="btn">← Back to Search</a>
    </div>
</div>
</body>
</html>
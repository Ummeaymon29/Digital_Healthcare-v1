<?php
session_start();
include "db.php";

// ১. সিকিউরিটি চেক: পেশেন্ট লগইন করা আছে কিনা
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Patient') {
    header("Location: login.html"); 
    exit();
}

// ২. ডাটাবেজ থেকে প্রেসক্রিপশন ডাটা আনা
$stmt = $conn->prepare("SELECT p.*, u.name as doctor_name, c.date as consult_date 
                        FROM prescriptions p 
                        JOIN users u ON p.doctor_id = u.id 
                        JOIN consultations c ON p.consultation_id = c.id 
                        WHERE p.patient_id = ? ORDER BY p.created_at DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Prescriptions</title>
    <style>
        :root{--primary:#2563eb;--bg:#f8fafc;--card:#fff;}
        body{font-family:'Segoe UI',sans-serif;background:var(--bg);padding:30px;color:#1e293b;}
        .container{max-width:800px;margin:0 auto;}
        h1{margin:0 0 20px;}
        .presc-card{background:var(--card);padding:20px;border-radius:12px;margin-bottom:15px;box-shadow:0 3px 8px rgba(0,0,0,.06);}
        .meta{color:#64748b;font-size:14px;margin-bottom:10px;}
        .med-table{width:100%;border-collapse:collapse;margin:10px 0;}
        .med-table th, .med-table td{padding:10px;border:1px solid #e2e8f0;text-align:left;font-size:14px;}
        .med-table th{background:#f1f5f9;}
        .notes{background:#f8fafc;padding:10px;border-left:3px solid var(--primary);border-radius:4px;margin-top:10px;font-size:14px;}
        .back{color:#64748b;text-decoration:none;font-size:14px;display:inline-block;margin-bottom:20px;}
        .back:hover{color:var(--primary);}
    </style>
</head>
<body>
<div class="container">
    <a href="patient_dashboard.php" class="back">← Back to Dashboard</a>
    <h1>💊 My Prescriptions</h1>

    <!-- লুপ শুরু: ডাটাবেজ থেকে যতগুলো প্রেসক্রিপশন আসবে, একে একে দেখাবে -->
    <?php while($row = $result->fetch_assoc()): 
        // JSON ডাটা থেকে ওষুধের লিস্ট বের করা
        $meds = json_decode($row['medicines'], true); 
        // যদি ডাটা ঠিক না থাকে তবে খালি অ্যারে ধরে নেওয়া
        if (!is_array($meds)) $meds = []; 
    ?>
    
    <div class="presc-card">
        <!-- হেডার: ডক্টরের নাম ও তারিখ -->
        <div class="meta">
            👨‍⚕️ Dr. <?=htmlspecialchars($row['doctor_name'])?> | 
            📅 <?=htmlspecialchars($row['prescription_date'])?>
        </div>
        
        <!-- ওষুধের টেবিল -->
        <table class="med-table">
            <tr>
                <th>Medicine</th>
                <th>Dosage</th>
                <th>Frequency</th>
                <th>Duration</th>
            </tr>
            <?php foreach($meds as $m): ?>
            <tr>
                <td><?=htmlspecialchars($m['name'])?></td>
                <td><?=htmlspecialchars($m['dosage'])?></td>
                <td><?=htmlspecialchars($m['frequency'])?></td>
                <td><?=htmlspecialchars($m['duration'])?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <!-- ডক্টরের নোট (যদি থাকে) -->
        <?php if(!empty($row['notes'])): ?>
            <div class="notes">
                📝 <?=nl2br(htmlspecialchars($row['notes']))?>
            </div>
        <?php endif; ?>
    </div>

    <?php endwhile; ?>
    <!-- লুপ শেষ -->

    <!-- যদি কোনো প্রেসক্রিপশন না থাকে -->
    <?php if(mysqli_num_rows($result) == 0): ?>
        <p style="color:#64748b;text-align:center;">No prescriptions found yet.</p>
    <?php endif; ?>

</div>
</body>
</html>
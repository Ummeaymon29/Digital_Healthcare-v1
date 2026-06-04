<?php
session_start();
include "db.php";
date_default_timezone_set('Asia/Dhaka');


if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role'] ?? '') !== 'patient') { header("Location: login.html?msg=Please+login+first&type=error"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_SESSION['user_id'];
    $med = trim($_POST['medicine_name']);
    $dosage = trim($_POST['dosage']);
    $time = $_POST['reminder_time'];
    $freq = $_POST['frequency'];
    $start = $_POST['start_date'];
    $end = !empty($_POST['end_date']) ? $_POST['end_date'] : NULL;

    $stmt = $conn->prepare("INSERT INTO reminders (patient_id, medicine_name, dosage, reminder_time, frequency, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $patient_id, $med, $dosage, $time, $freq, $start, $end);

    if ($stmt->execute()) {
        header("Location: my_reminders.php?msg=Reminder+added+successfully&type=success");
    } else {
        header("Location: add_reminder.php?msg=Database+error&type=error");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Medicine Reminder</title>
<style>
:root{--primary:#2563eb;--bg:#f8fafc;--card:#fff;}
body{font-family:'Segoe UI',sans-serif;background:var(--bg);padding:30px;color:#1e293b;}
.container{max-width:600px;margin:0 auto;}
.card{background:var(--card);padding:25px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.06);}
h2{margin:0 0 20px;color:var(--primary);}
.form-group{margin-bottom:15px;}
label{display:block;margin-bottom:6px;font-weight:500;font-size:14px;}
input,select{width:100%;padding:12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;}
.btn{width:100%;padding:12px;background:var(--primary);color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;margin-top:10px;}
.btn-back{background:#64748b;margin-top:8px;text-align:center;display:block;text-decoration:none;}
.alert{padding:12px;border-radius:8px;margin-bottom:15px;font-size:14px;}
.success{background:#dcfce7;color:#166534;} .error{background:#fee2e2;color:#991b1b;}
</style></head>
<body>
<div class="container">
    <div class="card">
        <h2>⏰ Set Medicine Reminder</h2>
        <form action="add_reminder.php" method="POST">
            <div class="form-group"><label>Medicine Name</label>
                <input type="text" name="medicine_name" placeholder="e.g. Napa, Seclo" required></div>
            <div class="form-group"><label>Dosage</label>
                <input type="text" name="dosage" placeholder="e.g. 500mg, 1 tablet"></div>
            <div class="form-group"><label>Reminder Time</label>
                <input type="time" name="reminder_time" required></div>
            <div class="form-group"><label>Frequency</label>
                <select name="frequency" required>
                    <option value="Once Daily">Once Daily</option>
                    <option value="Twice Daily">Twice Daily</option>
                    <option value="Thrice Daily">Thrice Daily</option>
                    <option value="As Needed">As Needed</option>
                </select></div>
            <div class="form-group"><label>Start Date</label>
                <input type="date" name="start_date" value="<?=date('Y-m-d')?>" required></div>
            <div class="form-group"><label>End Date (Optional)</label>
                <input type="date" name="end_date"></div>
            <button type="submit" class="btn"> Save Reminder</button>
            <a href="patient_dashboard.php" class="btn btn-back">← Back to Dashboard</a>
        </form>
    </div>
</div></body></html>
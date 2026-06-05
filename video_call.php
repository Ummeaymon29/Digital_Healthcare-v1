<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include "db.php";
if (!isset($_SESSION['user_id'])) { header("Location: login.html"); exit(); }
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) die("Invalid ID");
$stmt = $conn->prepare("SELECT zoom_meeting_id, zoom_meeting_pwd, doctor_id, patient_id FROM consultations WHERE id=?");
$stmt->bind_param("i", $id); $stmt->execute(); $call = $stmt->get_result()->fetch_assoc();
if (!$call || empty($call['zoom_meeting_id'])) die("Zoom not configured.");
$uid = (int)$_SESSION['user_id'];
if ($uid !== (int)$call['doctor_id'] && $uid !== (int)$call['patient_id']) die("⛔ Access Denied.");
$zoom_url = "https://zoom.us/j/{$call['zoom_meeting_id']}" . (!empty($call['zoom_meeting_pwd']) ? "?pwd={$call['zoom_meeting_pwd']}" : "");
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Video Call</title>
<style>body{font-family:sans-serif;background:#f8fafc;text-align:center;padding:50px;} .card{background:#fff;padding:30px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.1);max-width:500px;margin:auto;} .btn{display:inline-block;padding:12px 24px;background:#2563eb;color:#fff;text-decoration:none;border-radius:8px;margin:10px 5px;}</style></head>
<body><div class="card"><h2>📹 Consultation #<?=$id?></h2><p>Zoom security blocks iframe. Open in new tab:</p><a href="<?=$zoom_url?>" target="_blank" class="btn">🔗 Join Zoom Meeting</a><a href="end_consultation.php?id=<?=$id?>" class="btn" style="background:#dc2626;">🔴 End Consultation</a></div>
</body>
</html>
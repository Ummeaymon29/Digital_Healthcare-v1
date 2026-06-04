<?php
session_start(); include "db.php";
// ❌ পুরনো লাইন মুছুন
// ✅ নতুন লাইন বসান
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role'] ?? '') !== 'doctor') { header("Location: login.html"); exit(); }
$consultation_id = (int)($_GET['id'] ?? $_POST['consultation_id'] ?? 0);
$msg = $_GET['msg'] ?? ''; $type = $_GET['type'] ?? '';
$stmt = $conn->prepare("SELECT patient_id, status FROM consultations WHERE id=? AND doctor_id=?");
$stmt->bind_param("ii", $consultation_id, $_SESSION['user_id']);
$stmt->execute(); $consult = $stmt->get_result()->fetch_assoc();
if (!$consult || ($consult['status'] !== 'completed' && $consult['status'] !== 'ongoing')) { header("Location: doctor_dashboard.php?msg=Invalid+consultation&type=error"); exit(); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medicines = json_encode($_POST['medicines'] ?? []);
    $notes = trim($_POST['notes'] ?? '');
    $stmt = $conn->prepare("INSERT INTO prescriptions (consultation_id, patient_id, doctor_id, prescription_date, medicines, notes) VALUES (?, ?, ?, CURDATE(), ?, ?)");
    $stmt->bind_param("iisss", $consultation_id, $consult['patient_id'], $_SESSION['user_id'], $medicines, $notes);
    $stmt->execute() ? header("Location: doctor_dashboard.php?msg=Prescription+saved&type=success") : header("Location: add_prescription.php?id=$consultation_id&msg=Error&type=error");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Prescription</title>
<style>
:root{--primary:#2563eb;--bg:#f8fafc;--card:#fff;--border:#e2e8f0;}
body{font-family:'Segoe UI',sans-serif;background:var(--bg);padding:30px;color:#1e293b;}
.container{max-width:750px;margin:0 auto;}
.card{background:var(--card);padding:25px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.06);border:1px solid var(--border);}
h2{margin:0 0 20px;color:var(--primary);}
.med-row{display:flex;gap:10px;margin-bottom:10px;flex-wrap:wrap;}
.med-row input{flex:1;padding:10px;border:1px solid var(--border);border-radius:6px;}
.btn{padding:10px 16px;border:none;border-radius:6px;cursor:pointer;font-weight:500;color:#fff;}
.btn-primary{background:var(--primary);} .btn-add{background:#10b981;} .btn-back{background:#64748b;text-decoration:none;}
textarea{width:100%;padding:10px;border:1px solid var(--border);border-radius:6px;resize:vertical;min-height:70px;margin-top:10px;}
.alert{padding:12px;border-radius:8px;margin-bottom:15px;font-size:14px;} .success{background:#dcfce7;color:#166534;} .error{background:#fee2e2;color:#991b1b;}
</style></head>
<body>
<div class="container">
<div class="card">
    <h2>📝 Add Prescription</h2>
    <?php if($msg): ?><div class="alert <?=$type?>"><?=$msg?></div><?php endif; ?>
    <form method="POST">
        <input type="hidden" name="consultation_id" value="<?=$consultation_id?>">
        <div id="medContainer">
            <div class="med-row">
                <input type="text" name="medicines[0][name]" placeholder="Medicine Name" required>
                <input type="text" name="medicines[0][dosage]" placeholder="Dosage" required>
                <input type="text" name="medicines[0][frequency]" placeholder="Frequency" required>
                <input type="text" name="medicines[0][duration]" placeholder="Duration" required>
            </div>
        </div>
        <button type="button" class="btn btn-add" onclick="addMedicine()">+ Add Medicine</button>
        <textarea name="notes" placeholder="Doctor's Notes..."></textarea>
        <div style="margin-top:15px;display:flex;gap:10px;">
            <button type="submit" class="btn btn-primary">💾 Save</button>
            <a href="doctor_dashboard.php" class="btn btn-back">← Back</a>
        </div>
    </form>
</div>
</div>
<script>
let medCount = 1;
function addMedicine() {
    const c = document.getElementById('medContainer');
    c.insertAdjacentHTML('beforeend', `<div class="med-row"><input name="medicines[${medCount}][name]" placeholder="Medicine" required><input name="medicines[${medCount}][dosage]" placeholder="Dosage" required><input name="medicines[${medCount}][frequency]" placeholder="Frequency" required><input name="medicines[${medCount}][duration]" placeholder="Duration" required></div>`);
    medCount++;
}
</script>
</body></html>
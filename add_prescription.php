<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Doctor') {
    header("Location: login.html"); exit();
}

// ✅ FIX: GET বা POST দুই জায়গা থেকেই ID নিতে পারবে
$consultation_id = (int)($_GET['id'] ?? $_POST['consultation_id'] ?? 0);
$msg = $_GET['msg'] ?? ''; $type = $_GET['type'] ?? '';

// ভেরিফিকেশন
$stmt = $conn->prepare("SELECT patient_id, status FROM consultations WHERE id=? AND doctor_id=?");
$stmt->bind_param("ii", $consultation_id, $_SESSION['user_id']);
$stmt->execute();
$res = $stmt->get_result();
$consult = $res->fetch_assoc();

// ✅ FIX: 'completed' OR 'ongoing' দুটোই এক্সেপ্ট করবে
if (!$consult || ($consult['status'] !== 'completed' && $consult['status'] !== 'ongoing')) {
    header("Location: doctor_dashboard.php?msg=Invalid+consultation&type=error"); exit();
}

// ফর্ম সাবমিশন
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medicines = json_encode($_POST['medicines'] ?? []);
    $notes = trim($_POST['notes'] ?? '');
    $date = date('Y-m-d');

    $stmt = $conn->prepare("INSERT INTO prescriptions (consultation_id, patient_id, doctor_id, prescription_date, medicines, notes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisss", $consultation_id, $consult['patient_id'], $_SESSION['user_id'], $date, $medicines, $notes);

    if ($stmt->execute()) {
        header("Location: doctor_dashboard.php?msg=Prescription+saved+successfully&type=success");
    } else {
        header("Location: add_prescription.php?id=$consultation_id&msg=Database+error&type=error");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Prescription</title>
<style>
:root{--primary:#2563eb;--bg:#f8fafc;--card:#fff;}
body{font-family:'Segoe UI',sans-serif;background:var(--bg);padding:30px;color:#1e293b;}
.container{max-width:700px;margin:0 auto;}
.card{background:var(--card);padding:25px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.06);}
h2{margin:0 0 20px;color:var(--primary);}
.med-row{display:flex;gap:10px;margin-bottom:10px;flex-wrap:wrap;}
.med-row input{flex:1;padding:10px;border:1px solid #e2e8f0;border-radius:6px;}
.btn{padding:10px 16px;border:none;border-radius:6px;cursor:pointer;font-weight:500;}
.btn-primary{background:var(--primary);color:#fff;}
.btn-add{background:#10b981;color:#fff;}
.btn-back{background:#64748b;color:#fff;text-decoration:none;}
.alert{padding:12px;border-radius:8px;margin-bottom:15px;font-size:14px;}
.success{background:#dcfce7;color:#166534;} .error{background:#fee2e2;color:#991b1b;}
textarea{width:100%;padding:10px;border:1px solid #e2e8f0;border-radius:6px;resize:vertical;min-height:80px;}
</style></head>
<body>
<div class="container">
    <div class="card">
        <h2>📝 Add Prescription</h2>
        <?php if($msg): ?><div class="alert <?=$type?>"><?=$msg?></div><?php endif; ?>
        
        <!-- ✅ FIX: হিডেন ফিল্ড যোগ করা হলো যাতে সাবমিটে ID হারিয়ে না যায় -->
        <form action="add_prescription.php" method="POST" id="prescForm">
            <input type="hidden" name="consultation_id" value="<?php echo $consultation_id; ?>">
            
            <div id="medContainer">
                <div class="med-row">
                    <input type="text" name="medicines[0][name]" placeholder="Medicine Name" required>
                    <input type="text" name="medicines[0][dosage]" placeholder="Dosage (e.g. 500mg)" required>
                    <input type="text" name="medicines[0][frequency]" placeholder="Frequency (e.g. 2x daily)" required>
                    <input type="text" name="medicines[0][duration]" placeholder="Duration (e.g. 7 days)" required>
                </div>
            </div>
            <button type="button" class="btn btn-add" onclick="addMedicine()">+ Add Medicine</button>
            <br><br>
            <textarea name="notes" placeholder="Doctor's Notes / Advice..."></textarea>
            <br><br>
            <button type="submit" class="btn btn-primary">💾 Save Prescription</button>
            <a href="doctor_dashboard.php" class="btn btn-back">← Back</a>
        </form>
    </div>
</div>
<script>
let medCount = 1;
function addMedicine() {
    const container = document.getElementById('medContainer');
    const row = `<div class="med-row">
        <input type="text" name="medicines[${medCount}][name]" placeholder="Medicine Name" required>
        <input type="text" name="medicines[${medCount}][dosage]" placeholder="Dosage" required>
        <input type="text" name="medicines[${medCount}][frequency]" placeholder="Frequency" required>
        <input type="text" name="medicines[${medCount}][duration]" placeholder="Duration" required>
    </div>`;
    container.insertAdjacentHTML('beforeend', row);
    medCount++;
}
</script>
</body></html>
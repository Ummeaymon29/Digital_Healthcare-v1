<?php
session_start();
include "db.php";
date_default_timezone_set('Asia/Dhaka');

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['role'] ?? '') !== 'patient') { header("Location: login.html?msg=Please+login+first&type=error"); exit(); }
$patient_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$now = date('H:i:s');
$msg = $_GET['msg'] ?? ''; $type = $_GET['type'] ?? '';

// Handle Mark as Taken/Missed
if (isset($_POST['action']) && isset($_POST['rem_id'])) {
    $rem_id = (int)$_POST['rem_id'];
    $status = $_POST['action'] === 'take' ? 'taken' : 'missed';
    $stmt = $conn->prepare("UPDATE reminders SET status=? WHERE id=? AND patient_id=?");
    $stmt->bind_param("sii", $status, $rem_id, $patient_id);
    $stmt->execute();
    header("Location: my_reminders.php"); exit();
}

// Fetch today's active reminders
$stmt = $conn->prepare("SELECT * FROM reminders 
                        WHERE patient_id=? AND start_date<=? AND (end_date IS NULL OR end_date>=?) 
                        ORDER BY reminder_time ASC");
$stmt->bind_param("iss", $patient_id, $today, $today);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Medicine Reminders</title>
<style>
:root{--primary:#2563eb;--bg:#f8fafc;--card:#fff;--success:#10b981;--warning:#f59e0b;--danger:#ef4444;}
body{font-family:'Segoe UI',sans-serif;background:var(--bg);padding:30px;color:#1e293b;}
.container{max-width:800px;margin:0 auto;}
h1{margin:0 0 20px;}
.rem-card{background:var(--card);padding:18px;border-radius:12px;margin-bottom:15px;box-shadow:0 3px 8px rgba(0,0,0,.06);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;}
.rem-info h3{margin:0 0 5px;font-size:18px;color:var(--primary);}
.rem-info p{margin:0;color:#64748b;font-size:14px;}
.badge{padding:5px 10px;border-radius:15px;font-size:12px;font-weight:600;}
.pending{background:#fef3c7;color:#92400e;} .taken{background:#dcfce7;color:#166534;} .missed{background:#fee2e2;color:#991b1b;}
.actions{display:flex;gap:8px;}
.btn-sm{padding:8px 12px;border:none;border-radius:6px;font-size:13px;cursor:pointer;color:#fff;font-weight:500;}
.btn-take{background:var(--success);} .btn-skip{background:var(--warning);} .btn-view{background:#64748b;text-decoration:none;padding:8px 12px;border-radius:6px;font-size:13px;color:#fff;}
.back{color:#64748b;text-decoration:none;font-size:14px;display:inline-block;margin-bottom:20px;}
</style></head>
<body>
<div class="container">
    <a href="patient_dashboard.php" class="back">← Back to Dashboard</a>
    <h1>💊 Today's Medicine Reminders</h1>
    
    <a href="add_reminder.php" class="btn-add" style="display:inline-block;margin-bottom:15px;padding:8px 14px;background:#10b981;color:#fff;border-radius:6px;text-decoration:none;font-size:14px;">➕ Add New Reminder</a>
<?php if($msg): ?><div class="alert <?=$type?>" style="max-width:800px;margin:0 auto 15px;padding:12px;border-radius:8px;font-size:14px;"><?=$msg?></div><?php endif; ?>

<?php if(mysqli_num_rows($result) > 0): 
    while($row = $result->fetch_assoc()): 
        $is_overdue = ($row['status'] == 'pending' && $now > $row['reminder_time']);
        $badgeClass = $row['status'] == 'taken' ? 'taken' : ($row['status'] == 'missed' ? 'missed' : 'pending');
?>
<div class="rem-card" style="<?= $is_overdue ? 'border-left:4px solid #ef4444;' : '' ?>">
    <div class="rem-info">
        <h3>💊 <?=htmlspecialchars($row['medicine_name'])?></h3>
        <p>⏰ <?=date('h:i A', strtotime($row['reminder_time']))?> | 🔄 <?=htmlspecialchars($row['frequency'])?></p>
        <?php if(!empty($row['dosage'])): ?><p>📏 <?=htmlspecialchars($row['dosage'])?></p><?php endif; ?>
    </div>
    <div class="actions">
        <span class="badge <?=$badgeClass?>"><?=ucfirst($row['status'])?></span>
        <?php if($row['status'] == 'pending'): ?>
        <form method="POST" style="display:inline;">
            <input type="hidden" name="rem_id" value="<?=$row['id']?>">
            <button type="submit" name="action" value="take" class="btn-sm btn-take">✅ Taken</button>
            <button type="submit" name="action" value="skip" class="btn-sm btn-skip">⏭️ Missed</button>
        </form>
        <?php endif; ?>
    </div>
</div>
<?php endwhile; 
else: ?>
    <p style="color:#64748b;text-align:center;">No reminders scheduled for today.</p>
<?php endif; ?>
    <!-- 🔔 Real-Time Alert System -->
<script>
let notifReady = false;
if ("Notification" in window) {
    if (Notification.permission === "granted") {
        notifReady = true;
    } else if (Notification.permission !== "denied") {
        // প্রথমবার পেজে ক্লিক করলে পারমিশন চাইবে
        document.addEventListener('click', function askPerm() {
            Notification.requestPermission().then(p => {
                if (p === "granted") {
                    notifReady = true;
                    console.log("✅ নোটিফিকেশন পারমিশন চালু!");
                }
            });
            document.removeEventListener('click', askPerm);
        }, { once: true });
    }
}

// নোটিফিকেশন পাঠানোর ফাংশন
function sendMedNotification(med, dosage) {
    if (!notifReady) {
        console.warn(" নোটিফিকেশন পারমিশন দেওয়া নেই। শুধু সাউন্ড/পপআপ কাজ করবে।");
        return;
    }
    try {
        const n = new Notification("⏰ ওষুধ খাওয়ার সময়!", {
            body: med + (dosage ? "\n" + dosage : ""),
            icon: "💊",
            tag: "med-reminder-" + Date.now(), // ডুপ্লিকেট বন্ধ করে
            requireInteraction: false
        });
        n.onclick = () => { window.focus(); n.close(); };
        console.log("🔔 নোটিফিকেশন সফলভাবে পাঠানো হয়েছে!");
    } catch (e) {
        console.error("Notification Error:", e);
    }
}

// ️ প্রতি 5 সেকেন্ডে চেক (আগের মতোই, শুধু নোটিফিকেশন কল পরিবর্তন)
let lastAlerted = new Set();
setInterval(() => {
    const now = new Date();
    const curH = now.getHours(), curM = now.getMinutes();
    const curTime = `${String(curH).padStart(2,'0')}:${String(curM).padStart(2,'0')}`;
    
    document.querySelectorAll('.rem-card').forEach(card => {
        const p = card.querySelector('.rem-info p');
        if (!p) return;
        const match = p.textContent.match(/⏰\s*(\d{1,2}):(\d{2})\s*([AP]M)/i);
        if (!match) return;
        
        let h = parseInt(match[1]), m = parseInt(match[2]);
        const ampm = match[3].toUpperCase();
        if (ampm === 'PM' && h !== 12) h += 12;
        if (ampm === 'AM' && h === 12) h = 0;
        
        const remTime = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`;
        const key = card.dataset.id || card.innerHTML.slice(0, 20);
        
        if (remTime === curTime && !lastAlerted.has(key)) {
            lastAlerted.add(key);
            const med = card.querySelector('h3').textContent;
            const dos = card.querySelector('.rem-info p:nth-of-type(2)')?.textContent || '';
            
            // 🔊 সাউন্ড
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain); gain.connect(ctx.destination);
            osc.frequency.value = 800; osc.type = 'sine'; gain.gain.value = 0.3;
            osc.start(); setTimeout(() => { osc.stop(); ctx.close(); }, 2000);
            
            
            // 🎨 হাইলাইট
            card.style.border = '3px solid #10b981';
            card.style.boxShadow = '0 0 25px rgba(16,185,129,0.6)';
            setTimeout(() => { card.style.border = ''; card.style.boxShadow = ''; }, 5000);
            
            // ️ পপআপ
            setTimeout(() => alert(`⏰ MEDICINE TIME!\n\n${med}\n${dos}`), 500);
            
            //  ঘণ্টা পর আবার চেক করার সুযোগ
            setTimeout(() => lastAlerted.delete(key), 3600000);
        }
    });
}, 5000);
</script>

<!-- 🎨 CSS Animation -->
<style>
@keyframes pulseAlert {
    0%, 100% { 
        transform: scale(1); 
        box-shadow: 0 0 0 rgba(16,185,129,0);
    }
    25% { 
        transform: scale(1.02); 
        box-shadow: 0 0 20px rgba(16,185,129,0.5);
    }
    50% { 
        transform: scale(1); 
        box-shadow: 0 0 0 rgba(16,185,129,0);
    }
    75% { 
        transform: scale(1.02); 
        box-shadow: 0 0 20px rgba(16,185,129,0.5);
    }
}
</style>
</div></body></html>
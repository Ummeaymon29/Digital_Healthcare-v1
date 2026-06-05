<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// ✅ আগে থেকে লগইন থাকলে ড্যাশবোর্ডে নিয়ে যাবে
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $role = trim(strtolower($_SESSION['role']));
    if ($role === 'doctor') {
        header("Location: doctor_dashboard.php");
        exit();
    } else {
        header("Location: patient_dashboard.php");
        exit();
    }
}

include 'db.php';
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = trim($_POST['role'] ?? 'Patient'); // ✅ রোল ইনপুট যোগ
$message = ''; 
$messageType = '';
$formData = ['email' => $email, 'role' => $role]; // ✅ ফর্ম ডাটা রিটেন করার জন্য

// ✅ POST রিকোয়েস্ট হলেই লগিন প্রসেস করবে
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($email) || empty($password)) {
        $message = "Email and password are required!"; 
        $messageType = "error";
    } else {
        $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // ✅ রোল লোয়ারকেসে সেভ করুন (consistent comparison)
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = strtolower($user['role']);
                $_SESSION['name'] = $user['name'];
                
                if ($_SESSION['role'] === 'doctor') {
                    header("Location: doctor_dashboard.php");
                } else {
                    header("Location: patient_dashboard.php");
                }
                exit(); // 🔥 জরুরি
            } else { 
                $message = "Incorrect password!"; 
                $messageType = "error"; 
            }
        } else { 
            $message = "No account found with this email!"; 
            $messageType = "error"; 
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Digital Healthcare</title>
    <style>
        body{font-family:'Segoe UI',sans-serif;background:#f8fafc;display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0;padding:20px;}
        .card{background:#fff;padding:30px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,.1);max-width:400px;width:100%;}
        h2{margin:0 0 20px;text-align:center;color:#1e293b;}
        .form-group{margin-bottom:15px;}
        label{display:block;margin-bottom:6px;font-weight:500;font-size:14px;color:#334155;}
        input,select{width:100%;padding:12px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;}
        input:focus,select:focus{outline:none;border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.1);}
        .btn{width:100%;padding:12px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;font-size:15px;margin-top:10px;}
        .btn:hover{background:#1d4ed8;}
        .alert{padding:12px;border-radius:8px;margin-bottom:15px;font-size:14px;text-align:center;}
        .error{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;}
        .success{background:#dcfce7;color:#166534;border:1px solid #86efac;}
        .link{text-align:center;margin-top:15px;font-size:14px;}
        .link a{color:#2563eb;text-decoration:none;}
        .link a:hover{text-decoration:underline;}
    </style>
</head>
<body>
<div class="card">
    <h2>🔐 Login</h2>
    
    <!-- ✅ এরর/সাকসেস মেসেজ দেখানোর অংশ -->
    <?php if(!empty($message)): ?>
        <div class="alert <?=$messageType?>"><?=htmlspecialchars($message)?></div>
    <?php endif; ?>
    
    <!-- ✅ লগইন ফর্ম (GET/POST উভয় ক্ষেত্রেই দেখাবে) -->
    <form action="login.php" method="POST">
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" value="<?=htmlspecialchars($formData['email'] ?? '')?>" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>I am a</label>
            <select name="role">
                <option value="Patient" <?=($formData['role'] ?? '') === 'Patient' ? 'selected' : ''?>>👤 Patient</option>
                <option value="Doctor" <?=($formData['role'] ?? '') === 'Doctor' ? 'selected' : ''?>>🩺 Doctor</option>
            </select>
        </div>
        <button type="submit" class="btn">Login to Account</button>
    </form>
    
    <div class="link">
        Don't have an account? <a href="register.html">✨ Register now</a>
    </div>
</div>
</body>
</html>
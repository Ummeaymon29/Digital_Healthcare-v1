<?php
session_start();
include 'db.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'Patient';

$message = '';
$messageType = '';

if (empty($email) || empty($password)) {
    $message = "Email and password are required!";
    $messageType = "error";
} else {
    $email_esc = mysqli_real_escape_string($conn, $email);
    $sql = "SELECT * FROM users WHERE email='$email_esc'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            // Optional: verify role matches selection
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            
            if ($user['role'] == 'Doctor') {
                header("Location: doctor_dashboard.php");
            } else {
                header("Location: patient_dashboard.php");
            }
            exit();
        } else {
            $message = "Incorrect password!";
            $messageType = "error";
        }
    } else {
        $message = "No account found with this email!";
        $messageType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Status</title>
<style>
body{font-family:'Segoe UI',sans-serif;background:#f8fafc;display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0;padding:20px;}
.card{background:#fff;padding:30px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,.1);max-width:450px;width:100%;text-align:center;}
.icon{font-size:48px;margin-bottom:15px;}
h2{margin:10px 0;color:#1e293b;}
.message{padding:12px;border-radius:8px;margin:15px 0;font-size:14px;}
.error{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;}
.btn{display:inline-block;padding:10px 24px;background:#2563eb;color:#fff;text-decoration:none;border-radius:8px;font-weight:500;margin-top:10px;transition:.3s;}
.btn:hover{background:#1d4ed8;}
</style>
</head>
<body>
<div class="card">
<div class="icon">⚠️</div>
<h2>Login Failed</h2>
<div class="message error"><?php echo htmlspecialchars($message); ?></div>
<a href="login.html" class="btn">Try Again</a>
</div>
</body>
</html>
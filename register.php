<?php
include 'db.php';

// Get & sanitize form values
$fullname = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'Patient';
$phone = trim($_POST['phone'] ?? '');

$message = '';
$messageType = ''; // success or error

if (empty($fullname) || empty($email) || empty($password)) {
    $message = "All required fields must be filled!";
    $messageType = "error";
} else {
    $email = mysqli_real_escape_string($conn, $email);
    $fullname = mysqli_real_escape_string($conn, $fullname);
    
    // Check if user exists
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $message = "User with this email already exists!";
        $messageType = "error";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $phone_esc = mysqli_real_escape_string($conn, $phone);
        
        $sql = "INSERT INTO users (name, email, password, role, phone) 
                VALUES ('$fullname', '$email', '$hashed', '$role', '$phone_esc')";
        
        if (mysqli_query($conn, $sql)) {
            $message = "Registration successful! Redirecting to login...";
            $messageType = "success";
            header("refresh:2;url=login.html");
        } else {
            $message = "Error: " . mysqli_error($conn);
            $messageType = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registration Status</title>
<style>
body{font-family:'Segoe UI',sans-serif;background:#f8fafc;display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0;padding:20px;}
.card{background:#fff;padding:30px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,.1);max-width:450px;width:100%;text-align:center;}
.icon{font-size:48px;margin-bottom:15px;}
h2{margin:10px 0;color:#1e293b;}
.message{padding:12px;border-radius:8px;margin:15px 0;font-size:14px;}
.success{background:#dcfce7;color:#166534;border:1px solid #86efac;}
.error{background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;}
.btn{display:inline-block;padding:10px 24px;background:#2563eb;color:#fff;text-decoration:none;border-radius:8px;font-weight:500;margin-top:10px;transition:.3s;}
.btn:hover{background:#1d4ed8;}
</style>
</head>
<body>
<div class="card">
<div class="icon"><?php echo $messageType==='success'?'✅':'⚠️'; ?></div>
<h2><?php echo $messageType==='success'?'Registration Successful!':'Registration Failed'; ?></h2>
<div class="message <?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
<a href="login.html" class="btn">Go to Login</a>
</div>
</body>
</html>
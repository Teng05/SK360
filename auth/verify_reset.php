<?php
require_once '../classes/database.php';
session_start();
date_default_timezone_set('Asia/Manila');

$db = new Database();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    
    $conn = $db->openConnection();
    
    // 1. Check if code exists and is not expired
    $stmt = $conn->prepare("SELECT user_id, reset_id FROM password_resets WHERE reset_code = ? AND expires_at > NOW() LIMIT 1");
    $stmt->execute([$code]);
    $reset = $stmt->fetch();

    if ($reset) {
        $_SESSION['reset_user_id'] = $reset['user_id'];
        $_SESSION['code_verified'] = true; 

        // Use your actual column name 'reset_id'
        $del = $conn->prepare("DELETE FROM password_resets WHERE reset_id = ?");
        $del->execute([$reset['reset_id']]);

        header("Location: update_password.php");
        exit();
    } else {
        $error = "The code is invalid or has expired.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code | SK360</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --sk-red: #d32f2f; }
        body { background: #f2f6fb; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', sans-serif; }
        .auth-card { background: white; border-radius: 30px; padding: 40px; width: 100%; max-width: 400px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); border: none; }
        .code-input { letter-spacing: 12px; font-size: 28px; text-align: center; font-weight: 800; border-radius: 12px; background: #f8f9fa; border: 2px solid #eee; padding: 15px; }
        .code-input:focus { border-color: var(--sk-red); box-shadow: none; background: white; }
        .btn-sk { background: var(--sk-red); color: white; border-radius: 12px; padding: 14px; width: 100%; border: none; font-weight: 600; margin-top: 10px; transition: 0.3s; }
        .btn-sk:hover { background: #b71c1c; color: white; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="auth-card text-center">
        <div class="mb-3 text-danger">
            <i class="bi bi-shield-check" style="font-size: 3rem;"></i>
        </div>
        <h2 style="color: var(--sk-red); font-weight: 700;">Verification</h2>
        <p class="text-muted small mb-4">Enter the 6-digit security code sent to your device to continue.</p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small border-0 shadow-sm mb-4">
                <i class="bi bi-exclamation-circle me-2"></i><?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <input type="text" 
                       name="code" 
                       id="otp-input"
                       class="form-control code-input" 
                       maxlength="6" 
                       placeholder="000000" 
                       required 
                       autocomplete="one-time-code"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '');">
            </div>
            <button type="submit" class="btn btn-sk">Verify & Continue</button>
        </form>
        
        <div class="mt-4">
            <a href="resetpass.php" class="text-muted small text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Didn't get a code? Try again
            </a>
        </div>
    </div>

    <script>
        // Auto-focus input on load
        window.onload = () => document.getElementById('otp-input').focus();
    </script>
</body>
</html>
<?php
require_once '../classes/database.php';
session_start();
$db = new Database();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    
    // Check if code exists and is not expired
    $conn = $db->openConnection();
    $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE reset_code = ? AND expires_at > NOW() LIMIT 1");
    $stmt->execute([$code]);
    $reset = $stmt->fetch();

    if ($reset) {
        // SUCCESS: Store the User ID in a session so update_password.php knows who it is
        $_SESSION['reset_user_id'] = $reset['user_id'];
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f2f6fb; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-card { background: white; border-radius: 30px; padding: 40px; width: 100%; max-width: 400px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); border: none; }
        .code-input { letter-spacing: 8px; font-size: 24px; text-align: center; font-weight: bold; border-radius: 12px; background: #f1f3f5; border: 2px solid transparent; }
        .code-input:focus { border-color: #d32f2f; box-shadow: none; background: white; }
        .btn-sk { background: #d32f2f; color: white; border-radius: 10px; padding: 12px; width: 100%; border: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="auth-card text-center">
        <h2 style="color: #d32f2f; font-weight: 700;">Enter Code</h2>
        <p class="text-muted small mb-4">Type the 6-digit code we sent you.</p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="code" class="form-control code-input mb-4" maxlength="6" placeholder="000000" required autocomplete="off">
            <button type="submit" class="btn btn-sk">Verify Code</button>
        </form>
        <a href="resetpass.php" class="d-block mt-3 text-muted small text-decoration-none">Didn't get a code? Try again</a>
    </div>
</body>
</html>
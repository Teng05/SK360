<?php
session_start();
require_once '../classes/database.php';

// Security check
if (!isset($_SESSION['reset_user_id']) || !isset($_SESSION['code_verified'])) {
    header("Location: resetpass.php");
    exit();
}

$db = new Database();
$message = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    // 1. Validation Logic
    if ($new_pass !== $confirm_pass) {
        $message = "Passwords do not match!";
    } elseif (strlen($new_pass) < 8) {
        $message = "Password must be at least 8 characters long.";
    } else {
        // 2. Database Logic (Calling the class method)
        $user_id = $_SESSION['reset_user_id'];
        
        if ($db->updatePassword($user_id, $new_pass)) {
            $success = true;
            // Clear sessions to prevent re-entry
            session_destroy();
        } else {
            $message = "Database error. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Password | SK360</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f2f6fb; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', sans-serif; }
        .auth-card { background: white; border-radius: 30px; padding: 40px; width: 100%; max-width: 400px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .btn-sk { background: #d32f2f; color: white; border-radius: 10px; width: 100%; padding: 12px; border: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="auth-card">
        <h2 class="text-center mb-4" style="color: #d32f2f; font-weight: 700;">New Password</h2>
        
        <?php if ($success): ?>
            <div class="alert alert-success text-center">
                Password updated successfully! <br>
                <a href="login.php" class="fw-bold text-decoration-none">Login Now</a>
            </div>
        <?php else: ?>
            <?php if ($message): ?>
                <div class="alert alert-danger small"><?= $message ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold">New Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Min. 8 characters">
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required placeholder="Repeat password">
                </div>
                <button type="submit" class="btn btn-sk">Update Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
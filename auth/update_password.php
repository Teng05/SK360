<?php
require_once '../classes/database.php';
session_start();

// Check if user actually passed the verification step
if (!isset($_SESSION['reset_user_id'])) {
    header("Location: resetpass.php");
    exit();
}

$db = new Database();
$user_id = $_SESSION['reset_user_id'];
$error = "";
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    if (strlen($new_pass) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif ($new_pass !== $confirm_pass) {
        $error = "Passwords do not match.";
    } else {
        // Use the updatePassword method in your Database class
        if ($db->updatePassword($user_id, $new_pass)) {
            $success = true;
            unset($_SESSION['reset_user_id']); // Clear the session for security
        } else {
            $error = "Database error. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f2f6fb; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-card { background: white; border-radius: 30px; padding: 40px; width: 100%; max-width: 400px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .btn-sk { background: #d32f2f; color: white; border-radius: 10px; padding: 12px; width: 100%; border: none; font-weight: 600; }
        .form-control { border-radius: 10px; padding: 12px; background: #f1f3f5; border: none; }
    </style>
</head>
<body>
    <div class="auth-card">
        <?php if ($success): ?>
            <div class="text-center">
                <h2 style="color: #2e7d32;">Success!</h2>
                <p class="text-muted">Password has been reset.</p>
                <a href="login.php" class="btn btn-sk">Go to Login</a>
            </div>
        <?php else: ?>
            <h2 class="text-center mb-4" style="color: #d32f2f; font-weight: 700;">New Password</h2>
            <?php if ($error): ?> <div class="alert alert-danger small py-2"><?= $error ?></div> <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="small fw-bold">Enter New Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-4">
                    <label class="small fw-bold">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-sk">Update Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
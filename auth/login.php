<?php
require_once '../classes/database.php';
session_start();

$db = new Database();
$errors = [];
$success_message = "";

// Handle POST login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $errors[] = "All fields are required.";
    } else {
        $user = $db->loginUser($email, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['first_name'] = $user['first_name'];

            $success_message = "Login successful! Redirecting...";
        } else {
            // Check why login failed
            $stmt = $db->openConnection()->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $checkUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($checkUser) {
                if ($checkUser['status'] !== 'active') {
                    $errors[] = "Your account is inactive. Contact admin.";
                } elseif (!$checkUser['is_verified']) {
                    $errors[] = "Email not verified. Please check your inbox.";
                } else {
                    $errors[] = "Incorrect password.";
                }
            } else {
                $errors[] = "No account found with this email.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SK 360° | Lipa City Youth Governance</title>
<link rel="stylesheet" href="../css/login.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container">

    <div class ="left-panel">
        <div class ="back"><a href="../index.php"> Back to home</a></div>
        <h2 class ="logo">SK 360°</h2>
        <h1>Welcome Back!</h1>
        <p>Access your dashboard to manage reports, 
            coordinate with your team, 
            and drive youth governance forward.</p>

        <div class ="features">
            <div class = "feature">
                <span>🛡️</span>
                <div>
                    <b>Secure Access</b>
                <p>Role-based authentication for data protection</p>
                </div>
            </div>

            <div class = "feature">
                <span>📊</span>
                <div>
                    <b>Centralized Dashboard</b>
                <p>All your tools in one place</p>
                </div>
            </div>

            <div class = "feature">
                <span>🤝</span>
                <div>
                    <b>Real-Time Collaboration</b>
                <p>Connect with SK officials instantly</p>
                </div>
            </div>
        </div>   
    </div>

    <div class ="right-panel">
        <h2>Sign In</h2>
        <p class="subtitle">Enter your credentials to access your account</p>

        <form method="POST">

            <label>Email Address</label>
            <input type="email" name="email" placeholder="x.sk@gmail.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>

            <div class="options">
                <label>
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="#">Forgot Password?</a>
            </div>

            <button class="login-btn" type="submit">Sign In</button>

            <p class="register">
                Don’t have an account?
                <a href="register.php">Register here</a>
            </p>

        </form>
    </div>

</div>

<script>
// Show errors using SweetAlert
<?php if(!empty($errors)): ?>
Swal.fire({
    icon: 'error',
    title: 'Login Failed',
    html: '<?= implode("<br>", array_map('htmlspecialchars', $errors)) ?>'
});
<?php endif; ?>

// Show success message
<?php if($success_message): ?>
Swal.fire({
    icon: 'success',
    title: 'Success',
    text: '<?= addslashes($success_message) ?>',
    confirmButtonText: 'OK'
}).then(() => {
    <?php if(isset($_SESSION['role'])): ?>
        let role = "<?= $_SESSION['role'] ?>";
        if(role === 'youth') { window.location.href='../dashboard/youth.php'; }
        else { window.location.href='../dashboard/admin.php'; }
    <?php endif; ?>
});
<?php endif; ?>
</script>

</body>
</html>
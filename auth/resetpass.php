<?php
session_start();
require_once '../classes/database.php';

// Set PHP Timezone to match Philippines
date_default_timezone_set('Asia/Manila');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Twilio\Rest\Client;

require_once __DIR__ . '/../vendor/autoload.php';

$db = new Database();
$conn = $db->openConnection();

$message = "";

/* TWILIO CONFIG */
$twilioSid = 'YOUR_NEW_ACCOUNT_SID';
$twilioToken = 'YOUR_NEW_AUTH_TOKEN';
$verifyServiceSid = 'YOUR_VERIFY_SERVICE_SID';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        
        if (!empty($email)) {
            $stmt = $conn->prepare("SELECT user_id, first_name FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                $user_id = $user['user_id'];
                $verificationCode = random_int(100000, 999999);

                // FIX: Let MySQL calculate the expiration based on ITS OWN current time (NOW())
                $ins = $conn->prepare("INSERT INTO password_resets (user_id, reset_code, method, expires_at) 
                                       VALUES (?, ?, 'email', DATE_ADD(NOW(), INTERVAL 15 MINUTE))");
                $ins->execute([$user_id, $verificationCode]);
                
                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_user_id'] = $user_id;

                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'paulmonje123@gmail.com'; 
                    $mail->Password   = 'vrffgqfdpautwxsf';    
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('paulmonje123@gmail.com', 'SK360 Support');
                    $mail->addAddress($email, $user['first_name']);

                    $mail->isHTML(true);
                    $mail->Subject = 'SK360 Password Reset Code';
                    $mail->Body    = "
                        <div style='font-family: Arial, sans-serif; text-align: center; border: 1px solid #ddd; padding: 20px; border-radius: 15px;'>
                            <h2 style='color: #d32f2f;'>Reset Your Password</h2>
                            <p>Hello {$user['first_name']}, use the code below to reset your password:</p>
                            <h1 style='background: #f4f4f4; display: inline-block; padding: 10px 20px; letter-spacing: 5px; color: #333;'>{$verificationCode}</h1>
                            <p style='font-size: 0.8rem; color: #777;'>This code will expire in 15 minutes.</p>
                        </div>";

                    $mail->send();
                    echo "<script>window.onload = function(){ showSuccess('email', '$email'); }</script>";
                } catch (Exception $e) {
                    $message = "Mail Error: Check your SMTP settings.";
                }
            } else {
                $message = "No account found with that email address.";
            }
        }
    }

    // 2. HANDLE SMS REQUEST (Twilio)
    if (isset($_POST['phone'])) {
        $phone = trim($_POST['phone']);
        if (!empty($phone)) {
            $stmt = $conn->prepare("SELECT user_id FROM users WHERE phone_number = ? LIMIT 1");
            $stmt->execute([$phone]);
            $user = $stmt->fetch();

            if ($user) {
                try {
                    $twilio = new Client($twilioSid, $twilioToken);
                    $twilio->verify->v2->services($verifyServiceSid)
                        ->verifications
                        ->create($phone, "sms");

                    $_SESSION['reset_phone'] = $phone;
                    $_SESSION['reset_user_id'] = $user['user_id'];
                    echo "<script>window.onload = function(){ showSuccess('phone', '$phone'); }</script>";
                } catch (Exception $e) {
                    $message = "SMS Error: " . $e->getMessage();
                }
            } else {
                $message = "This phone number is not registered.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | SK360</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --sk-red: #d32f2f; --sk-light-bg: #f2f6fb; }
        body { background-color: var(--sk-light-bg); font-family: 'Segoe UI', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .auth-card { background: white; border-radius: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.1); max-width: 450px; width: 100%; padding: 40px; border: none; }
        .sk-logo { width: 80px; height: 80px; margin-bottom: 20px; filter: drop-shadow(0 10px 10px rgba(0,0,0,0.1)); background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .main-title { color: var(--sk-red); font-weight: 700; }
        .method-toggle { background-color: #fce4e4; border-radius: 12px; padding: 6px; display: flex; margin-bottom: 25px; }
        .btn-toggle { flex: 1; border-radius: 8px; padding: 8px; border: none; font-size: 0.9rem; font-weight: 600; transition: 0.3s; background: transparent; color: var(--sk-red); }
        .btn-toggle.active { background-color: var(--sk-red); color: white; }
        .form-control-custom { background-color: #f1f3f5; border: none; border-radius: 10px; padding: 12px 15px; }
        .btn-sk-primary { background-color: var(--sk-red); border: none; border-radius: 10px; padding: 12px; font-weight: 600; width: 100%; margin-top: 20px; color: white; }
        .btn-sk-primary:hover { background-color: #b71c1c; color: white; }
        .hidden { display: none !important; }
    </style>
</head>
<body>

    <div class="container text-center">
        <?php if($message): ?>
            <div class="alert alert-danger mx-auto mb-4" style="max-width: 450px;"><?= $message ?></div>
        <?php endif; ?>

        <div id="request-view">
            <div class="d-flex justify-content-center">
                <div class="sk-logo shadow-sm"><i class="bi bi-shield-lock-fill fs-1 text-danger"></i></div>
            </div>
            <h2 class="main-title">Reset Your Password</h2>
            <p class="text-muted small mb-4">Choose your preferred reset method</p>

            <div class="auth-card mx-auto">
                <div class="method-toggle">
                    <button class="btn-toggle active" id="btn-email" onclick="switchMethod('email')"><i class="bi bi-envelope-fill me-2"></i>Email</button>
                    <button class="btn-toggle" id="btn-phone" onclick="switchMethod('phone')"><i class="bi bi-phone-fill me-2"></i>Phone</button>
                </div>

                <div id="input-email-group">
                    <form method="POST">
                        <input type="email" name="email" class="form-control form-control-custom mb-3" placeholder="Enter your email" required>
                        <button type="submit" class="btn btn-sk-primary">Send Reset Code</button>
                    </form>
                </div>

                <div id="input-phone-group" class="hidden">
                    <form method="POST">
                        <input type="text" name="phone" class="form-control form-control-custom mb-3" placeholder="+639..." required>
                        <button type="submit" class="btn btn-sk-primary">Send Reset Code</button>
                    </form>
                </div>

                <a href="login.php" class="d-block mt-4 text-muted text-decoration-none small"><i class="bi bi-arrow-left"></i> Back to Login</a>
            </div>
        </div>

        <div id="success-view" class="hidden">
            <div class="d-flex justify-content-center">
                <div class="sk-logo shadow-sm bg-warning text-white"><i class="bi bi-send-check fs-1"></i></div>
            </div>
            <h2 class="main-title">Code Sent!</h2>
            <div class="auth-card mx-auto">
                <div class="alert alert-warning py-2 small fw-bold mb-4" id="target-val"></div>
                <p class="text-muted small mb-4">Please enter the 6-digit verification code to set your new password.</p>
                <button class="btn btn-sk-primary" onclick="window.location.href='verify_reset.php'">Enter Code</button>
                <a href="#" class="d-block mt-3 text-danger fw-bold text-decoration-none small" onclick="location.reload()">Try Different Method</a>
            </div>
        </div>
    </div>

    <script>
        function switchMethod(method) {
            const isEmail = method === 'email';
            document.getElementById('input-email-group').classList.toggle('hidden', !isEmail);
            document.getElementById('input-phone-group').classList.toggle('hidden', isEmail);
            document.getElementById('btn-email').classList.toggle('active', isEmail);
            document.getElementById('btn-phone').classList.toggle('active', !isEmail);
        }

        function showSuccess(method, value) {
            document.getElementById('request-view').classList.add('hidden');
            document.getElementById('success-view').classList.remove('hidden');
            document.getElementById('target-val').innerText = value;
        }
    </script>
</body>
</html>
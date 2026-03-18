<?php
session_start();
require_once '../classes/database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: register.php");
    exit;
}

$db = new Database();
$conn = $db->openConnection();

$message = "";
$email = $_SESSION['verify_email'] ?? "";

// VERIFY CODE
if (isset($_POST['verify'])) {
    $code = implode("", $_POST['code'] ?? []);
    $code = preg_replace('/\D/', '', $code);
    $user_id = $_SESSION['user_id'];

    if (strlen($code) === 6) {
        $stmt = $conn->prepare("
            SELECT * FROM email_verifications
            WHERE user_id = ?
              AND verification_code = ?
              AND expires_at > NOW()
            LIMIT 1
        ");
        $stmt->execute([$user_id, $code]);
        $verify = $stmt->fetch();

        if ($verify) {
            $stmt = $conn->prepare("UPDATE users SET is_verified = 1, status = 'active' WHERE user_id = ?");
            $stmt->execute([$user_id]);

            $stmt = $conn->prepare("DELETE FROM email_verifications WHERE user_id = ?");
            $stmt->execute([$user_id]);

            unset($_SESSION['user_id'], $_SESSION['verify_email']);

            header("Location: login.php?verified=1");
            exit;
        } else {
            $message = "Invalid or expired code.";
        }
    } else {
        $message = "Please enter the complete 6-digit code.";
    }
}

// RESEND CODE
if (isset($_POST['resend'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT email, first_name FROM users WHERE user_id = ? LIMIT 1");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user) {
        $new_code = random_int(100000, 999999);

        $stmt = $conn->prepare("
            UPDATE email_verifications
            SET verification_code = ?, expires_at = DATE_ADD(NOW(), INTERVAL 1 HOUR)
            WHERE user_id = ?
        ");
        $stmt->execute([$new_code, $user_id]);

        try {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'your_email@gmail.com';
            $mail->Password   = 'your_16_character_app_password';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('your_email@gmail.com', 'SK360');
            $mail->addAddress($user['email'], $user['first_name']);

            $mail->isHTML(true);
            $mail->Subject = 'SK360 Verification Code';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif;'>
                    <h2>Your new verification code</h2>
                    <h1 style='letter-spacing: 4px; color: #D32F2F;'>{$new_code}</h1>
                    <p>This code will expire in 1 hour.</p>
                </div>
            ";

            $mail->send();
            $message = "A new code has been sent to your email.";

        } catch (Exception $e) {
            $message = "Failed to send email. Check your SMTP settings.";
        }
    } else {
        $message = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify Account</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}
.code-input:focus {
    outline: 2px solid #D32F2F;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.code-input');

    inputs.forEach((input, index) => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });

    if (inputs.length > 0) {
        inputs[0].focus();
    }
});
</script>
</head>
<body class="bg-[#F2F6FB] flex items-center justify-center min-h-screen">

<div class="w-[740px] text-center p-8">
    <h1 class="text-3xl font-semibold text-[#D32F2F] mb-3">Verify your Account</h1>
    <p class="text-sm text-[#5D6269] mb-3">Enter the 6-digit code sent to your email</p>

    <?php if (!empty($email)): ?>
        <p class="text-sm text-[#8F763F] mb-6 font-medium"><?= htmlspecialchars($email) ?></p>
    <?php endif; ?>

    <?php if (!empty($message)): ?>
        <div class="text-red-600 font-semibold mb-4">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white max-w-[430px] mx-auto p-12 rounded-[24px] shadow">
        <form method="POST">
            <div class="grid grid-cols-6 gap-3 mb-8">
                <input type="text" name="code[]" maxlength="1" class="code-input h-[60px] text-center text-2xl bg-gray-100 rounded">
                <input type="text" name="code[]" maxlength="1" class="code-input h-[60px] text-center text-2xl bg-gray-100 rounded">
                <input type="text" name="code[]" maxlength="1" class="code-input h-[60px] text-center text-2xl bg-gray-100 rounded">
                <input type="text" name="code[]" maxlength="1" class="code-input h-[60px] text-center text-2xl bg-gray-100 rounded">
                <input type="text" name="code[]" maxlength="1" class="code-input h-[60px] text-center text-2xl bg-gray-100 rounded">
                <input type="text" name="code[]" maxlength="1" class="code-input h-[60px] text-center text-2xl bg-gray-100 rounded">
            </div>

            <button name="verify" class="w-full bg-red-600 text-white py-3 rounded mb-4">
                Verify Account
            </button>
        </form>

        <form method="POST">
            <button name="resend" class="text-red-600 font-semibold">
                Resend Code
            </button>
        </form>
    </div>
</div>

</body>
</html>
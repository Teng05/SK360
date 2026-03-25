<?php
require_once '../classes/database.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../phpmailer/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../phpmailer/phpmailer/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../phpmailer/phpmailer/phpmailer/src/Exception.php';

$db = new Database();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name   = trim($_POST['first_name'] ?? '');
    $last_name    = trim($_POST['last_name'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $barangay_id  = (int) ($_POST['barangay_id'] ?? 0);
    $password     = $_POST['password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    if (!$first_name || !$last_name || !$email || !$phone_number || !$barangay_id || !$password || !$confirm_pass) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if ($password !== $confirm_pass) {
        $errors[] = "Passwords do not match.";
    }
/*
    if ($password && $confirm_pass &&)
*/
    if (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password)
    ) {
        $errors[] = "Password must be at least 8 characters and include uppercase, lowercase, and a number.";
    }

    if (!preg_match('/^\d{10,11}$/', $phone_number)) {
        $errors[] = "Phone number must be 10–11 digits.";
    }

    if ($db->isEmailExists($email)) {
        $errors[] = "Email is already registered.";
    }

    if (empty($errors)) {
        try {
            $result = $db->registerYouth($first_name, $last_name, $email, $phone_number, $barangay_id, $password);

            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['verify_email'] = $email;

            $verification_code = $result['verification_code'];

            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'paulmonje123@gmail.com';
            $mail->Password   = 'vrffgqfdpautwxsf';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('paulmonje123@gmail.com', 'SK360');
            $mail->addAddress($email, $first_name . ' ' . $last_name);

            $mail->isHTML(true);
            $mail->Subject = 'SK360 Verification Code';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif;'>
                    <h2>Verify your account</h2>
                    <p>Hello {$first_name},</p>
                    <p>Your verification code is:</p>
                    <h1 style='letter-spacing: 4px; color: #D32F2F;'>{$verification_code}</h1>
                    <p>This code will expire in 1 hour.</p>
                </div>
            ";
$mail->SMTPDebug = 2;
$mail->Debugoutput = 'html';
            $mail->send();

            header("Location: verify.php");
            exit;

        } catch (Exception $e) {
            $errors[] = "Account created, but the verification email could not be sent. Mailer Error: " . $mail->ErrorInfo;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

$barangays = $db->getBarangays();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SK 360 Register</title>
<link rel="stylesheet" href="../css/register.css">
<style>
input, select { display:block; margin-bottom:5px; padding:8px; width:100%; box-sizing:border-box; }
small.error-msg { color:red; font-size:12px; display:block; margin-bottom:10px; }
input.valid { border: 2px solid green; }
input.invalid { border: 2px solid red; }
button:disabled { background-color: gray; cursor: not-allowed; }
.hidden { display:none; }
.errors { background:#ffe5e5; color:#b00020; padding:10px; margin-bottom:15px; border-radius:8px; }
</style>
</head>
<body>

<div class="container">
    <div class="left">
        <div class="back"><a href="../index.php">Back to home</a></div>
        <h2>SK 360°</h2>
        <h3>Join SK 360°</h3>
        <p>Create your account to access the centralized platform for youth governance in Lipa City.</p>
        <div class="steps">
            <div class="step"><div class="circle">1</div><b>Personal Information</b></div>
            <div class="step"><div class="circle">2</div><b>Security Setup</b></div>
        </div>
        <hr>
        <div class="signin-link">
            <p>Already have an account?</p>
            <a href="login.php">🔑 Sign In Here</a>
        </div>
    </div>

    <div class="right">
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $err): ?>
                    <p><?= htmlspecialchars($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form id="registerForm" method="POST">
            <div id="info">
                <h2>Create Account</h2>
                <p class="subtitle">Fill in your information to get started</p>

                <label>First Name</label>
                <input type="text" name="first_name" placeholder="Juan" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">

                <label>Last Name</label>
                <input type="text" name="last_name" placeholder="Dela Cruz" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">

                <label>Email Address</label>
                <input type="email" name="email" placeholder="sk360@gmail.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

                <label>Phone Number</label>
                <input type="text" name="phone_number" placeholder="09123456789" maxlength="11" value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>">

                <label>Barangay</label>
                <select name="barangay_id">
                    <option value="">Select your barangay</option>
                    <?php foreach($barangays as $b): ?>
                        <option value="<?= $b['barangay_id'] ?>" <?= (($_POST['barangay_id'] ?? '') == $b['barangay_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['barangay_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="button" onclick="nextStep()">Continue</button>
            </div>

            <div id="pass" class="hidden">
                <h2>Set Your Password</h2>
                <p class="subtitle">Choose a strong password for your account</p>

                <label>Password</label>
                <input type="password" name="password" placeholder="Create a strong password">

                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Re-enter your password">

                <div class="btn-group">
                    <button type="button" class="back-btn" onclick="prevStep()">Back</button>
                    <button type="submit">Create Account</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registerForm');
    const continueBtn = document.querySelector('#info button');
    const submitBtn = document.querySelector('#pass button[type="submit"]');

    // Fields
    const firstName = form.querySelector('input[name="first_name"]');
    const lastName = form.querySelector('input[name="last_name"]');
    const email = form.querySelector('input[name="email"]');
    const phone = form.querySelector('input[name="phone_number"]');
    const barangay = form.querySelector('select[name="barangay_id"]');
    const password = form.querySelector('input[name="password"]');
    const confirmPassword = form.querySelector('input[name="confirm_password"]');

    // Helper: create or get <small> for error messages
    function getErrorElem(field) {
        let el = field.nextElementSibling;
        if (!el || !el.classList.contains('error-msg')) {
            el = document.createElement('small');
            el.classList.add('error-msg');
            field.insertAdjacentElement('afterend', el);
        }
        return el;
    }

    // Validation functions
    const validators = {
        firstName: val => val.trim() !== '',
        lastName: val => val.trim() !== '',
        email: val => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val),
        phone: val => /^\d{10,11}$/.test(val),
        barangay: val => val !== '',
        password: val => /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/.test(val),
        confirmPassword: val => val === password.value
    };

    // Track validity
    const fieldValidity = {
        firstName: false,
        lastName: false,
        email: false,
        phone: false,
        barangay: false,
        password: false,
        confirmPassword: false
    };

    // Set field validity classes
    function setFieldValidity(field, isValid, message='') {
        const errorEl = getErrorElem(field);
        if (isValid) {
            field.classList.add('valid');
            field.classList.remove('invalid');
            errorEl.textContent = '';
        } else {
            field.classList.add('invalid');
            field.classList.remove('valid');
            errorEl.textContent = message;
        }
    }

    // Real-time field validation (non-AJAX)
    function attachValidation(field, key, customMsg) {
        field.addEventListener('input', () => {
            const isValid = validators[key](field.value);
            fieldValidity[key] = isValid;
            setFieldValidity(field, isValid, isValid ? '' : customMsg);
            toggleButtons();
        });
    }

    attachValidation(firstName, 'firstName', 'First name is required.');
    attachValidation(lastName, 'lastName', 'Last name is required.');
    attachValidation(barangay, 'barangay', 'Please select a barangay.');
    attachValidation(password, 'password', 'Password must be 8+ chars with uppercase, lowercase, and number.');
    attachValidation(confirmPassword, 'confirmPassword', 'Passwords do not match.');

    // Real-time AJAX Email check
    email.addEventListener('input', () => {
        const val = email.value.trim();
        if (!validators.email(val)) {
            fieldValidity.email = false;
            setFieldValidity(email, false, 'Please enter a valid email format.');
            toggleButtons();
            return;
        }

        // AJAX call
        fetch('../ajax/check_email.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `email=${encodeURIComponent(val)}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.exists) {
                fieldValidity.email = false;
                setFieldValidity(email, false, 'Email is already registered.');
            } else {
                fieldValidity.email = true;
                setFieldValidity(email, true);
            }
            toggleButtons();
        })
        .catch(() => {
            fieldValidity.email = false;
            setFieldValidity(email, false, 'Error checking email.');
            toggleButtons();
        });
    });

    // Real-time AJAX Phone check
    phone.addEventListener('input', () => {
        const val = phone.value.trim();
        if (!validators.phone(val)) {
            fieldValidity.phone = false;
            setFieldValidity(phone, false, 'Phone number must be 10–11 digits.');
            toggleButtons();
            return;
        }

        // AJAX call
        fetch('../ajax/check_phone.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `phone_number=${encodeURIComponent(val)}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.exists) {
                fieldValidity.phone = false;
                setFieldValidity(phone, false, 'Phone number is already registered.');
            } else {
                fieldValidity.phone = true;
                setFieldValidity(phone, true);
            }
            toggleButtons();
        })
        .catch(() => {
            fieldValidity.phone = false;
            setFieldValidity(phone, false, 'Error checking phone number.');
            toggleButtons();
        });
    });

    // Enable/disable buttons
    function toggleButtons() {
        // Continue button: only first step fields
        const firstStepValid = fieldValidity.firstName &&
                               fieldValidity.lastName &&
                               fieldValidity.email &&
                               fieldValidity.phone &&
                               fieldValidity.barangay;
        continueBtn.disabled = !firstStepValid;

        // Submit button: all fields
        const allValid = firstStepValid &&
                         fieldValidity.password &&
                         fieldValidity.confirmPassword;
        submitBtn.disabled = !allValid;
    }

    // Initial toggle
    toggleButtons();

    function nextStep() {
        document.getElementById("info").classList.add("hidden");
        document.getElementById("pass").classList.remove("hidden");
    }

    function prevStep() {
        document.getElementById("pass").classList.add("hidden");
        document.getElementById("info").classList.remove("hidden");
    }

    // Optional: prevent Continue button from moving if invalid
    continueBtn.addEventListener('click', (e) => {
        e.preventDefault(); // always prevent default submit or navigation

        const firstStepValid = fieldValidity.firstName &&
                            fieldValidity.lastName &&
                            fieldValidity.email &&
                            fieldValidity.phone &&
                            fieldValidity.barangay;

        if (firstStepValid) {
            // Move to password step only if valid
            nextStep();
        } else {
            // Optionally, highlight invalid fields or show a general error message
            alert('Please fix the errors before continuing.');
        }
    });

    // Optional: prevent form submit if invalid
    form.addEventListener('submit', (e) => {
        const allValid = Object.values(fieldValidity).every(v => v);
        if (!allValid) e.preventDefault();
    });
});
</script>

</body>
</html>
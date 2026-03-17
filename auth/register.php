<?php
require_once '../classes/database.php';
session_start();

$db = new Database();
$errors = [];
$success_message = "";

// Handle POST registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name   = trim($_POST['first_name'] ?? '');
    $last_name    = trim($_POST['last_name'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $barangay_id  = (int) ($_POST['barangay_id'] ?? 0);
    $password     = $_POST['password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    // Server-side validation
    if (!$first_name || !$last_name || !$email || !$phone_number || !$barangay_id || !$password) {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if ($password !== $confirm_pass) {
        $errors[] = "Passwords do not match.";
    }
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must be at least 8 characters and include uppercase, lowercase, and a number.";
    }
    if (!preg_match('/^\d{10,11}$/', $phone_number)) {
        $errors[] = "Phone number must be 10–11 digits.";
    }

    // Check duplicate email using database method
    if ($db->emailExists($email)) {
        $errors[] = "Email is already registered.";
    }

    // Register user
    if (empty($errors)) {
        $verification_code = $db->registerYouth($first_name, $last_name, $email, $phone_number, $barangay_id, $password);
        $success_message = "Account created! Please check your email to verify your account.";
        // TODO: send $verification_code via email
    }
}

// Fetch barangays
$barangays = $db->getBarangays();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SK 360 Register</title>
<link rel="stylesheet" href="../css/register.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
input, select { display:block; margin-bottom:5px; padding:8px; width:100%; box-sizing:border-box; }
small.error-msg { color:red; font-size:12px; display:block; margin-bottom:10px; }
input.valid { border: 2px solid green; }
input.invalid { border: 2px solid red; }
button:disabled { background-color: gray; cursor: not-allowed; }
</style>
</head>
<body>

<div class="container">
    <div class="left">
        <div class ="back"><a href="../index.php"> Back to home</a></div>
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
            <!-- STEP 1 -->
            <div id="info">
                <h2>Create Account</h2>
                <p class="subtitle">Fill in your information to get started</p>

                <label>First Name</label>
                <input type="text" name="first_name" placeholder="Juan" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
                <small class="error-msg"></small>

                <label>Last Name</label>
                <input type="text" name="last_name" placeholder="Dela Cruz" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
                <small class="error-msg"></small>

                <label>Email Address</label>
                <input type="email" name="email" placeholder="sk360@gmail.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <small class="error-msg"></small>

                <label>Phone Number</label>
                <input type="text" name="phone_number" placeholder="0912 345 6789" maxlength="11" value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>">
                <small class="error-msg"></small>

                <label>Barangay</label>
                <select name="barangay_id">
                    <option value="">Select your barangay</option>
                    <?php foreach($barangays as $b): ?>
                        <option value="<?= $b['barangay_id'] ?>" <?= (($_POST['barangay_id'] ?? '') == $b['barangay_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['barangay_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="error-msg"></small>

                <button type="button" onclick="nextStep()" id="step1Continue" disabled>Continue</button>
            </div>

            <!-- STEP 2 -->
            <div id="pass" class="hidden">
                <h2>Set Your Password</h2>
                <p class="subtitle">Choose a strong password for your account</p>

                <label>Password</label>
                <input type="password" name="password" placeholder="Create a strong password">
                <small class="error-msg"></small>

                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Re-enter your password">
                <small class="error-msg"></small>

                <div class="btn-group">
                    <button type="button" class="back-btn" onclick="prevStep()">Back</button>
                    <button type="submit" id="submitBtn" disabled>Create Account</button>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
// Multi-step
function nextStep(){
    document.getElementById("info").classList.add("hidden");
    document.getElementById("pass").classList.remove("hidden");
}
function prevStep(){
    document.getElementById("pass").classList.add("hidden");
    document.getElementById("info").classList.remove("hidden");
}

// Step 1 validation
const step1Inputs = document.querySelectorAll('#info input, #info select');
const step1Continue = document.getElementById('step1Continue');
const step2Inputs = document.querySelectorAll('#pass input');
const submitBtn = document.getElementById('submitBtn');

function validateStep1(){
    let valid = true;
    step1Inputs.forEach(input => {
        const msg = input.nextElementSibling;
        if(!input.value.trim()){ msg.textContent = 'This field is required.'; input.classList.add('invalid'); input.classList.remove('valid'); valid=false; }
        else{ msg.textContent=''; input.classList.add('valid'); input.classList.remove('invalid'); }

        if(input.name==='email'){
            const pattern=/^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if(!pattern.test(input.value.trim())){ msg.textContent='Invalid email address.'; input.classList.add('invalid'); input.classList.remove('valid'); valid=false; }
        }
    });

    // AJAX check email existence
    const emailInput = document.querySelector('input[name="email"]');
    if(emailInput.value.trim() !== ''){
        fetch('check_email.php?email=' + encodeURIComponent(emailInput.value.trim()))
        .then(res => res.json())
        .then(data=>{
            const msg=emailInput.nextElementSibling;
            if(data.exists){ msg.textContent='Email already exists.'; emailInput.classList.add('invalid'); emailInput.classList.remove('valid'); step1Continue.disabled=true; }
            else{ if(emailInput.classList.contains('invalid')){ msg.textContent=''; emailInput.classList.remove('invalid'); emailInput.classList.add('valid'); } step1Continue.disabled=!valid; }
        });
    } else { step1Continue.disabled=!valid; }

    step1Continue.disabled=!valid;
}

// Step 2 validation
function validateStep2(){
    let valid = true;
    const pw=document.querySelector('input[name="password"]').value;
    const cpw=document.querySelector('input[name="confirm_password"]').value;
    const pwField=document.querySelector('input[name="password"]');
    const cpwField=document.querySelector('input[name="confirm_password"]');
    const pwMsg=pwField.nextElementSibling;
    const cpwMsg=cpwField.nextElementSibling;

    const hasUpper=/[A-Z]/.test(pw);
    const hasLower=/[a-z]/.test(pw);
    const hasNumber=/[0-9]/.test(pw);

    if(pw.length<8 || !hasUpper || !hasLower || !hasNumber){
        pwMsg.textContent='Password must be 8+ chars with upper, lower, number.'; pwField.classList.add('invalid'); pwField.classList.remove('valid'); valid=false;
    } else { pwMsg.textContent=''; pwField.classList.add('valid'); pwField.classList.remove('invalid'); }

    if(pw!==cpw){ cpwMsg.textContent='Passwords do not match.'; cpwField.classList.add('invalid'); cpwField.classList.remove('valid'); valid=false; }
    else if(pw!==''){ cpwMsg.textContent=''; cpwField.classList.add('valid'); cpwField.classList.remove('invalid'); }

    submitBtn.disabled = !valid;
}

// Phone number live validation
const phoneInput = document.querySelector('input[name="phone_number"]');
phoneInput.addEventListener('input', () => {
    const msg = phoneInput.nextElementSibling;
    phoneInput.value = phoneInput.value.replace(/[^\d]/g, '');
    
    if(phoneInput.value.length < 10 || phoneInput.value.length > 11){
        msg.textContent = 'Phone number must be 10–11 digits.';
        phoneInput.classList.add('invalid'); 
        phoneInput.classList.remove('valid');
    } else {
        msg.textContent = '';
        phoneInput.classList.add('valid'); 
        phoneInput.classList.remove('invalid');
    }

    validateStep1();
});

// Event listeners
step1Inputs.forEach(i => i.addEventListener('input', validateStep1));
step2Inputs.forEach(i => i.addEventListener('input', validateStep2));

step1Continue.disabled=true;
submitBtn.disabled=true;
validateStep1();
validateStep2();
</script>

<?php if($success_message): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: '<?= addslashes($success_message) ?>',
    confirmButtonText: 'OK'
}).then(()=>{ window.location.href='login.php'; });
</script>
<?php endif; ?>

</body>
</html>
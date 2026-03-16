<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK 360 Register</title>
    <link rel="stylesheet" href="../css/register.css">
</head>
<body>
    <div class="container">
        <div class="left">
            <div class ="back"><a href="../index.php"> Back to home</a></div>
                <h2>SK 360°</h2>
            <h3>Join SK 360°</h3>
            <p>Create your account to access the centralized platform for youth governance in Lipa City.</p>
            <div class="steps">
                <div class="step">
                    <div class="circle">1</div>
                    <b>Personal Information</b>
                </div>
                <div class="step">
                    <div class="circle">2</div>
                    <b>Security Setup</b>
                </div>
            </div>
            <hr>
            <div class="signin-link">
                <p>Already have an account?</p>
                <a href="login.php">🔑 Sign In Here</a>
            </div>
        </div>
        <div class="right">
            <!-- STEP 1 -->
            <div id="info">
                <h2>Create Account</h2>
                <p class="subtitle">Fill in your information to get started</p>
                <form>
                    <label>First Name</label>
                    <input type="text" placeholder="Juan">
                    <label>Last Name</label>
                    <input type="text" placeholder="Dela Cruz">
                    <label>Email Address</label>
                    <input type="email" placeholder="sk360@gmail.com">
                    <label>Phone Number</label>
                    <input type="text" placeholder="0912 345 6789">
                    <label>Barangay</label>
                    <select>
                        <option>Select your barangay</option>
                        <option>Barangay 1</option>
                        <option>Barangay 2</option>
                        <option>Barangay 3</option>
                    </select>
                    <button type="button" onclick="nextStep()">Continue</button>
                </form>
            </div>
            <!-- STEP 2 -->
            <div id="pass" class="hidden">
                <h2>Set Your Password</h2>
                <p class="subtitle">Choose a strong password for your account</p>
                <form>
                    <label>Password</label>
                    <input type="password" placeholder="Create a strong password">
                    <label>Confirm Password</label>
                    <input type="password" placeholder="Re-enter your password">
                    <p class="notice">
                        Must be at least 8 characters with uppercase, lowercase, and number
                    </p>
                    <label>
                        <input type="checkbox"> I agree to the Terms of Service
                    </label>
                    <div class="btn-group">
                        <button type="button" class="back-btn" onclick="prevStep()">Back</button>
                        <button type="button" onclick="location.href='login.php'">Create Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function nextStep(){
            document.getElementById("info").classList.add("hidden");
            document.getElementById("pass").classList.remove("hidden");
        }
        function prevStep(){
            document.getElementById("pass").classList.add("hidden");
            document.getElementById("info").classList.remove("hidden");
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --sk-red: #d32f2f;
            --sk-light-bg: #f2f6fb;
            --sk-yellow-bg: #fefad4;
            --sk-yellow-border: #e9d9ab;
            --sk-yellow-text: #8f763f;
        }

        body {
            background-color: var(--sk-light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: screen;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-card {
            background: white;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 450px;
            width: 100%;
            padding: 40px;
            border: none;
        }

        .sk-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 20px;
            filter: drop-shadow(0 10px 10px rgba(0,0,0,0.1));
        }

        .main-title {
            color: var(--sk-red);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .sub-text {
            color: #6c757d;
            font-size: 0.85rem;
            margin-bottom: 25px;
        }

        /* Toggle Switch Styling */
        .method-toggle {
            background-color: #fce4e4;
            border-radius: 12px;
            padding: 6px;
            display: flex;
            margin-bottom: 25px;
        }

        .btn-toggle {
            flex: 1;
            border-radius: 8px;
            padding: 8px;
            border: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-toggle.active {
            background-color: var(--sk-red);
            color: white;
        }

        .btn-toggle:not(.active) {
            background: transparent;
            color: var(--sk-red);
        }

        /* Input Styling */
        .form-label-custom {
            font-size: 0.85rem;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .label-icon {
            background-color: #fce4e4;
            color: var(--sk-red);
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }

        .form-control-custom {
            background-color: #f1f3f5;
            border: none;
            border-radius: 10px;
            padding: 12px 15px;
        }

        .btn-sk-primary {
            background-color: var(--sk-red);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            margin-top: 20px;
            color: white;
        }

        .btn-sk-primary:hover {
            background-color: #b71c1c;
            color: white;
        }

        /* Success View Components */
        .success-icon-circle {
            background-color: #ffca28;
            color: white;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 20px;
        }

        .info-badge {
            background-color: var(--sk-yellow-bg);
            border: 1px solid var(--sk-yellow-border);
            color: var(--sk-yellow-text);
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 15px;
        }

        .expiry-alert {
            background-color: #fff5f5;
            border: 1px solid #ffebed;
            color: #d32f2f;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            margin: 15px 0;
        }

        .back-link {
            text-decoration: none;
            color: #6c757d;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            margin-top: 15px;
        }

        .hidden { display: none; }
    </style>
</head>
<body>

    <div class="container text-center">
        <div id="request-view">
            <div class="d-flex justify-content-center">
                <div class="sk-logo bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                    <i class="bi bi-person-walking fs-1 text-primary"></i> </div>
            </div>
            
            <h2 class="main-title">Reset Your Password</h2>
            <p class="sub-text">Choose your preferred reset method</p>

            <div class="auth-card mx-auto">
                <div class="method-toggle">
                    <button class="btn-toggle active" id="btn-email" onclick="switchMethod('email')">
                        <i class="bi bi-envelope-fill me-2"></i>Email
                    </button>
                    <button class="btn-toggle" id="btn-phone" onclick="switchMethod('phone')">
                        <i class="bi bi-phone-fill me-2"></i>Phone
                    </button>
                </div>

                <div id="input-email-group">
                    <label class="form-label-custom">
                        <span class="label-icon"><i class="bi bi-envelope"></i></span> Email Address
                    </label>
                    <input type="email" class="form-control form-control-custom" placeholder="sk360@gmail.com">
                    <button class="btn btn-sk-primary" onclick="showSuccess('email')">Send Reset Link</button>
                </div>

                <div id="input-phone-group" class="hidden">
                    <label class="form-label-custom">
                        <span class="label-icon"><i class="bi bi-phone"></i></span> Phone Number
                    </label>
                    <input type="text" class="form-control form-control-custom" placeholder="+63912345678">
                    <button class="btn btn-sk-primary" onclick="showSuccess('phone')">Send Reset Code</button>
                </div>

                <a href="#" class="back-link"><i class="bi bi-arrow-left"></i> Back to Login</a>
            </div>
        </div>

        <div id="success-view" class="hidden">
            <div class="success-icon-circle shadow-sm">
                <i class="bi bi-envelope-check"></i>
            </div>
            
            <h2 class="main-title" id="success-title">Reset Link Sent!</h2>
            <p class="sub-text" id="success-desc">Check your email for password reset instructions</p>

            <div class="auth-card mx-auto">
                <div class="info-badge">
                    <i class="bi bi-envelope"></i> <span id="target-val">sk360@gmail.com</span>
                </div>
                
                <p class="text-muted" style="font-size: 0.75rem;">
                    We've sent a instructions to your registered account. Please check your inbox and follow the instructions to reset your password.
                </p>

                <div class="expiry-alert">
                    <i class="bi bi-clock me-1"></i> Link expires in 15 minutes
                </div>

                <button class="btn btn-sk-primary">Back to Login</button>
                
                <a href="#" class="back-link text-danger fw-bold" onclick="resetFlow()">
                    <i class="bi bi-arrow-repeat"></i> Try Different Method
                </a>
            </div>
        </div>
    </div>

    <script>
        function switchMethod(method) {
            const emailGroup = document.getElementById('input-email-group');
            const phoneGroup = document.getElementById('input-phone-group');
            const btnEmail = document.getElementById('btn-email');
            const btnPhone = document.getElementById('btn-phone');

            if (method === 'email') {
                emailGroup.classList.remove('hidden');
                phoneGroup.classList.add('hidden');
                btnEmail.classList.add('active');
                btnPhone.classList.remove('active');
            } else {
                emailGroup.classList.add('hidden');
                phoneGroup.classList.remove('hidden');
                btnEmail.classList.remove('active');
                btnPhone.classList.add('active');
            }
        }

        function showSuccess(method) {
            document.getElementById('request-view').classList.add('hidden');
            document.getElementById('success-view').classList.remove('hidden');
            
            const title = document.getElementById('success-title');
            const desc = document.getElementById('success-desc');
            const target = document.getElementById('target-val');

            if (method === 'email') {
                title.innerText = "Reset Link Sent!";
                desc.innerText = "Check your email for password reset instructions";
                target.innerText = "sk360@gmail.com";
            } else {
                title.innerText = "Reset Code Sent!";
                desc.innerText = "Check your phone for password reset instructions";
                target.innerText = "+63912345678";
            }
        }

        function resetFlow() {
            document.getElementById('request-view').classList.remove('hidden');
            document.getElementById('success-view').classList.add('hidden');
        }
    </script>
</body>
</html>
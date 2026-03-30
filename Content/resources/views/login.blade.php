<!DOCTYPE html>
<html lang="en">
<head>
    <title>AIL-Surveyor Portal</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    
    
    <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    
    <style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: "Inter", sans-serif;
    }

    body {
        min-height: 100vh;
        background: linear-gradient(135deg, 
            #0d73c6 39.8%, 
            #0d73c6 40%,   
            #ffffff 40%,  
            #ffffff 40.2%, 
            #f7fbff 40.2%  
        );
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }
    
    .container {
        max-width: 1100px;
        width: 100%;
        display: flex;
        gap: 32px;
        padding: 10px;
    }

    .card {
        display: flex;
        width: 100%;
        background: #ffffff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(0, 0, 0, .08);
    }

    .left {
        flex: 1.2;
        background: linear-gradient(180deg, #e9f4ff 0%, #f3f9ff 85%);
        padding: 45px 45px 20px;
        position: relative;
    }

    .left img.logo {
        width: 180px;
        margin-bottom: 20px;
    }

    .left h2 {
        margin-top: 10px;
        font-size: 26px;
        color: #0d2545;
    }

    .left p {
        margin-top: 6px;
        color: #3a4a63;
        line-height: 1.5;
        margin-bottom: 25px;
    }

    .features {
        margin-top: 22px;
    }

    .features li {
        list-style: none;
        margin: 12px 0;
        display: flex;
        gap: 10px;
        color: #1c2e45;
        align-items: center;
    }

    .features li i {
        color: #1c2e45;
        width: 20px;
    }

    .illustration {
        position: absolute;
        bottom: 40px;
        right: 40px;
        width: 240px;
        height: 170px;
        opacity: .95;
        object-fit: contain;
        border-radius: 0;
        box-shadow: none;
        border: none;
        background: transparent;
    }

    .right {
        flex: 1;
        padding: 55px 45px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .right h3 {
        font-size: 24px;
        margin-bottom: 16px;
        color: #0d2545;
        font-weight: 700;
    }
    
    .instruction-text {
        background-color: #f8fbff;
        border-left: 4px solid #0d73c6;
        padding: 14px 16px;
        margin-bottom: 22px;
        border-radius: 0 8px 8px 0;
        font-size: 14px;
        line-height: 1.5;
        color: #000000;
        text-align: justify;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }
    
    .instruction-text i {
        color: #0d73c6;
        margin-right: 8px;
        font-size: 13px;
    }

    .input-icon {
        position: relative;
        margin-bottom: 18px;
    }

    .input-icon i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #7e8ca3;
        z-index: 1;
    }

    input {
        width: 100%;
        padding: 13px 45px 13px 40px;
        border: 1px solid #d9e2ef;
        border-radius: 10px;
        font-size: 14px;
        transition: border-color 0.3s;
        background: #ffffff;
    }

    input:focus {
        outline: none;
        border-color: #0d73c6;
        box-shadow: 0 0 0 2px rgba(13, 115, 198, 0.1);
    }
    
    input.error {
        border-color: #dc3545;
    }
    
    input.error:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.1);
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #7e8ca3;
        cursor: pointer;
        z-index: 2;
        transition: color 0.2s;
        background: none;
        border: none;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .password-toggle:hover {
        color: #0d73c6;
    }

    .password-toggle i {
        font-size: 16px;
    }

    /* Captcha Styles */
    .captcha-container {
        margin-bottom: 18px;
    }

    .captcha-box {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
    }

    .captcha-display {
        flex: 1;
        background: linear-gradient(135deg, #f0f4f8 0%, #e9f0f7 100%);
        border: 1px solid #d9e2ef;
        border-radius: 10px;
        padding: 12px 16px;
        font-size: 22px;
        font-weight: 700;
        letter-spacing: 8px;
        text-align: center;
        color: #0d2545;
        user-select: none;
        font-family: 'Courier New', monospace;
        text-decoration: line-through;
        text-decoration-color: rgba(13, 115, 198, 0.3);
        text-decoration-thickness: 1px;
    }

    .captcha-refresh {
        background: #ffffff;
        border: 1px solid #d9e2ef;
        border-radius: 10px;
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        color: #0d73c6;
    }

    .captcha-refresh:hover {
        background: #0d73c6;
        color: #ffffff;
        transform: rotate(180deg);
    }

    .captcha-refresh i {
        font-size: 18px;
    }

    .alert {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .alert-danger {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
    
    .alert-success {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: 8px;
        display: none;
        font-weight: 500;
        padding: 10px 12px;
        background-color: #f8d7da;
        border-left: 3px solid #dc3545;
        border-radius: 4px;
        animation: shake 0.3s ease-in-out;
    }
    
    .error-message.show {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .error-message i {
        font-size: 14px;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .actions {
        text-align: right;
        margin: 4px 0 20px;
    }

    .actions a {
        font-size: 14px;
        color: #0d73c6;
        text-decoration: none;
        cursor: pointer;
        font-weight: 500;
    }

    .actions a:hover {
        text-decoration: underline;
    }

    button {
        width: 100%;
        border: none;
        border-radius: 10px;
        padding: 14px;
        background: #0d73c6;
        color: #fff;
        font-weight: 600;
        cursor: pointer;
        font-size: 16px;
        transition: background 0.3s, transform 0.2s;
    }

    button:hover {
        background: #0b66af;
        transform: translateY(-1px);
    }

    .back-link {
        margin-top: 20px;
        font-size: 14px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: #0d73c6;
        cursor: pointer;
        text-decoration: none;
        font-weight: 500;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .login-form, .forgot-form {
        width: 100%;
    }

    .forgot-form {
        display: none;
    }

    .login100-form-title {
        font-size: 24px;
        color: #0d2545;
        margin-bottom: 30px;
        font-weight: 700;
        text-align: center;
    }

    @media (max-width: 900px) {
        .card {
            flex-direction: column;
        }
        
        .illustration {
            position: relative;
            bottom: auto;
            right: auto;
            width: 280px;
            height: 180px;
            margin: 20px auto 0;
        }
        
        .left, .right {
            padding: 30px;
        }
        
        .left {
            text-align: center;
        }
        
        .features li {
            justify-content: center;
        }
        
        .instruction-text {
            margin-bottom: 18px;
        }
    }
    
    @media (max-width: 480px) {
        .container {
            padding: 5px;
        }
        
        .left, .right {
            padding: 20px;
        }
        
        .left h2 {
            font-size: 22px;
        }
        
        .right h3 {
            font-size: 20px;
            margin-bottom: 14px;
        }
        
        .instruction-text {
            padding: 12px;
            font-size: 13px;
        }
        
        .illustration {
            width: 220px;
            height: 150px;
        }
        
        input {
            padding: 13px 45px 13px 40px;
        }
        
        .password-toggle {
            right: 12px;
        }

        .captcha-display {
            font-size: 18px;
            letter-spacing: 6px;
            padding: 10px 12px;
        }

        .captcha-refresh {
            width: 42px;
            height: 42px;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <!-- Left Panel -->
            <div class="left">
                <img class="logo" src="image/logo.png" alt="Atlas Insurance">
                
                <h2>Welcome to Atlas Insurance</h2>
                <p>Your trusted partner for a secure future.</p>
                
                <ul class="features">
                    <li><i class="fa-solid fa-shield-halved"></i>General Insurance</li>
                    <li><i class="fa-solid fa-car"></i>Motor & Travel Cover</li>
                    <li><i class="fa-solid fa-fire-extinguisher"></i>Marine & Fire Protection</li>
                    <li><i class="fa-solid fa-hand-holding-heart"></i>Window Takaful Operations</li>
                    <li><i class="fa-solid fa-circle-check"></i>AA Rated Financial Strength</li>
                </ul>
                
                <img class="illustration" src="image/insu_illustration.png" alt="Insurance Illustration">
            </div>
            
            <!-- Right Panel -->
            <div class="right">
                <!-- Login Form -->
                <form class="login-form validate-form" id="login-form" action="{{ url('/login') }}" method="post">
                    @csrf
                    <h3>Login to Surveyor Portal!</h3>
                    
                    @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif
                    
                    @error('status')
                    <div class="alert alert-danger">
                        {{ $message }}
                    </div>
                    @enderror
                    
                    <div class="input-icon">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" name="username" placeholder="Email Address" required>
                    </div>
                    
                    <div class="input-icon">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" id="password-field" placeholder="Password" required>
                        <span class="password-toggle" id="password-toggle">
                            <i class="fa-solid fa-eye"></i>
                        </span>
                    </div>

                    <!-- Captcha Section -->
                    <div class="captcha-container">
                        <div class="captcha-box">
                            <div class="captcha-display" id="captcha-display"></div>
                            <div class="captcha-refresh" id="captcha-refresh" title="Refresh Captcha">
                                <i class="fa-solid fa-rotate-right"></i>
                            </div>
                        </div>
                        <div class="input-icon">
                            <i class="fa-solid fa-shield"></i>
                            <input type="text" id="captcha-input" placeholder="Enter Captcha" required autocomplete="off">
                        </div>
                        <span class="error-message" id="captcha-error">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>Oops! The CAPTCHA didn’t match. Please try again</span>
                        </span>
                    </div>
                    
                    <div class="actions">
                        <a href="#" id="forgot-password-link">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" id="login-button">Login</button>
                </form>
                
                <!-- Forgot Password Form -->
                <form id="forgot-password-form" class="forgot-form validate-form" action="{{ url('/forgetPass') }}" method="post">
                    @csrf
                    <h3>Reset Password</h3>
                    
                    <div class="instruction-text">
                        <i class="fa-solid fa-info-circle"></i>
                        Enter your email address and we'll send you instructions to reset your password. 
                        Please also check your spam or junk folder if you don't see the email.
                    </div>
                    
                    @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                    @endif
                    
                    @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif
                    
                    <div class="input-icon">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" name="username" placeholder="Enter your Email" required>
                    </div>
                    
                    <button type="submit">Send Reset Link</button>
                    
                    <a class="back-link" href="#" id="back-to-login">
                        <i class="fa fa-long-arrow-left" aria-hidden="true"></i>
                        Back to Login
                    </a>
                </form>
            </div>
        </div>
    </div>

    
    <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="vendor/bootstrap/js/popper.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/select2/select2.min.js"></script>
    <script src="vendor/tilt/tilt.jquery.min.js"></script>
    <script src="js/main.js"></script>

    <script>
    
    let captchaText = '';

    function generateCaptcha() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let captcha = '';
        for (let i = 0; i < 6; i++) {
            captcha += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        captchaText = captcha;
        document.getElementById('captcha-display').textContent = captcha;
        document.getElementById('captcha-input').value = '';
        
        
        
    }

    function validateCaptcha() {
        const userInput = document.getElementById('captcha-input').value;
        const errorElement = document.getElementById('captcha-error');
        const inputElement = document.getElementById('captcha-input');
        
        if (userInput === captchaText) {
        
            if (errorElement) {
                errorElement.classList.remove('show');
            }
            if (inputElement) {
                inputElement.classList.remove('error');
            }
            return true;
        } else {
            
            if (errorElement) {
                errorElement.classList.add('show');
            }
            if (inputElement) {
                inputElement.classList.add('error');
                inputElement.focus();
            }
            
            
            generateCaptcha();
            
            
            setTimeout(() => {
                if (errorElement) {
                    errorElement.classList.add('show');
                }
            }, 10);
            
            return false;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        
        generateCaptcha();

        
        document.getElementById('captcha-refresh').addEventListener('click', function() {
            generateCaptcha();
            
            // When manually refreshing captcha, also clear any error
            const errorElement = document.getElementById('captcha-error');
            const inputElement = document.getElementById('captcha-input');
            if (errorElement) {
                errorElement.classList.remove('show');
            }
            if (inputElement) {
                inputElement.classList.remove('error');
            }
        });
        
        
        const captchaInput = document.getElementById('captcha-input');
        if (captchaInput) {
            captchaInput.addEventListener('input', function() {
                const errorElement = document.getElementById('captcha-error');
                if (errorElement && errorElement.classList.contains('show')) {
                    errorElement.classList.remove('show');
                    this.classList.remove('error');
                }
            });
        }

        // Password toggle functionality
        const passwordField = document.getElementById('password-field');
        const passwordToggle = document.getElementById('password-toggle');
        const eyeIcon = passwordToggle.querySelector('i');
        
        if (passwordToggle && passwordField) {
            passwordToggle.addEventListener('click', function() {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                
                if (type === 'text') {
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                } else {
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                }
            });
        }
        
        
        const loginForm = document.querySelector('.login-form');
        const forgotForm = document.getElementById('forgot-password-form');
        const forgotLink = document.getElementById('forgot-password-link');
        const backLink = document.getElementById('back-to-login');
        
        if (forgotLink && loginForm && forgotForm) {
            forgotLink.addEventListener('click', function(e) {
                e.preventDefault();
                loginForm.style.display = 'none';
                forgotForm.style.display = 'block';
                
                setTimeout(() => {
                    const emailInput = forgotForm.querySelector('input[name="username"]');
                    if (emailInput) emailInput.focus();
                }, 100);
            });
        }
        
        if (backLink && loginForm && forgotForm) {
            backLink.addEventListener('click', function(e) {
                e.preventDefault();
                forgotForm.style.display = 'none';
                loginForm.style.display = 'block';
                generateCaptcha(); 
                
                setTimeout(() => {
                    const emailInput = loginForm.querySelector('input[name="username"]');
                    if (emailInput) emailInput.focus();
                }, 100);
            });
        }
        
        // Check if we should show forgot password form
        const hasResetMessages = forgotForm.querySelector('.alert-success') || forgotForm.querySelector('.alert-danger');
        if (hasResetMessages && loginForm && forgotForm) {
            loginForm.style.display = 'none';
            forgotForm.style.display = 'block';
        }
        

        const card = document.querySelector('.card');
        if (card) {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        }

        
        const loginFormElement = document.getElementById('login-form');
        if (loginFormElement) {
            loginFormElement.addEventListener('submit', function(e) {
            
                if (!validateCaptcha()) {
                    e.preventDefault(); 
                    return false;
                }
                
                return true;
            });
        }
    });
    </script>

</body>
</html>
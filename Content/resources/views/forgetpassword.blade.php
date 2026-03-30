<!DOCTYPE html>
<html lang="en">
<head>
    <title>Atlas Insurance- Auto Secure</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('images/icons/favicon.ico') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/animate/animate.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/css-hamburgers/hamburgers.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/select2/select2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
    <style>
        /* Optional: Add some styling for visual feedback on validation */
        .wrap-input100.valid input {
            border-bottom: 2px solid green;
        }
        .wrap-input100.invalid input {
            border-bottom: 2px solid red;
        }
        .validation-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
        .wrap-input100.invalid .validation-message {
            display: block;
        }
    </style>
</head>
<body>

    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                <div class="login100-pic js-tilt" data-tilt>
                    <img src="{{ asset('image/Logo.jpg') }}" alt="Atlas Insurance">
                </div>

                <form class="login100-form validate-form" action="{{ url('/resetPassword') }}" method="post" onsubmit="return validateForm()">
                    @csrf
                    <span class="login100-form-title">
                        Reset Password
                    </span>

                    @error('status')
            			    <span style="color: red;">{{ $message }}</span>
					@enderror

                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="username" value="{{ $username }}">

                    <div class="wrap-input100 validate-input" data-validate="Valid email is required: ex@abc.xyz">
                        <input type="password" id="fnew" name="fnew" placeholder="New Password" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100 toggle-password" id="toggle-new-password">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </span>
                        <span class="validation-message" id="new-password-validation-message"></span>
                    </div>

                    <div class="wrap-input100 validate-input" data-validate="Password is required">
                        <input type="password" class="form-control" id="fconf" name="fconf" placeholder="Confirm Password" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100 toggle-password" id="toggle-confirm-password">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </span>
                        <span class="validation-message" id="confirm-password-validation-message"></span>
                    </div>

                    <div class="container-login100-form-btn">
                        <button type="submit" class="login100-form-btn">
                            Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('vendor/jquery/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/popper.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/select2.min.js') }}"></script>
    <script src="{{ asset('vendor/tilt/tilt.jquery.min.js') }}"></script>
    <script>
        $('.js-tilt').tilt({
            scale: 1.1
        })
    </script>
    <script src="{{ asset('js/main.js') }}"></script>

</body>
</html>

<script>
    function validateForm() {
        let newPassword = document.getElementById("fnew").value;
        let confirmPassword = document.getElementById("fconf").value;
        let passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]).{8,}$/;

        // Check password pattern for new password
        if (!passwordPattern.test(newPassword)) {
            alert("New password does not meet the requirements.");
            return false;
        }

        // Check if passwords match
        if (newPassword !== confirmPassword) {
            alert("New passwords do not match.");
            return false;
        }

        return true;
    }

    document.addEventListener('DOMContentLoaded', function () {
        const toggleNewPassword = document.getElementById('toggle-new-password');
        const newPasswordInput = document.getElementById('fnew');
        const newPasswordContainer = newPasswordInput.closest('.wrap-input100');
        const newPasswordValidationMessage = document.getElementById('new-password-validation-message');
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]).{8,}$/;

        const toggleConfirmPassword = document.getElementById('toggle-confirm-password');
        const confirmPasswordInput = document.getElementById('fconf');
        const confirmPasswordContainer = confirmPasswordInput.closest('.wrap-input100');
        const confirmPasswordValidationMessage = document.getElementById('confirm-password-validation-message');

        // Function to handle the toggle
        function handleToggle(toggleIcon, passwordInput) {
            toggleIcon.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                const icon = toggleIcon.querySelector('i');
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        }

        // Function to validate the password pattern in real-time
        function validatePassword(input, container, messageElement, isConfirmField = false) {
            const newPasswordValue = newPasswordInput.value;
            const confirmPasswordValue = confirmPasswordInput.value;

            if (isConfirmField) {
                if (newPasswordValue === confirmPasswordValue) {
                    container.classList.remove('invalid');
                    container.classList.add('valid');
                    messageElement.textContent = '';
                } else {
                    container.classList.remove('valid');
                    container.classList.add('invalid');
                    messageElement.textContent = 'Passwords do not match.';
                }
            } else {
                if (passwordPattern.test(input.value)) {
                    container.classList.remove('invalid');
                    container.classList.add('valid');
                    messageElement.textContent = '';
                } else {
                    container.classList.remove('valid');
                    container.classList.add('invalid');
                    messageElement.textContent = 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.';
                }
            }
        }

        // Apply the function to both password fields
        handleToggle(toggleNewPassword, newPasswordInput);
        handleToggle(toggleConfirmPassword, confirmPasswordInput);

        // Add real-time validation listeners
        newPasswordInput.addEventListener('keyup', () => validatePassword(newPasswordInput, newPasswordContainer, newPasswordValidationMessage));
        confirmPasswordInput.addEventListener('keyup', () => validatePassword(confirmPasswordInput, confirmPasswordContainer, confirmPasswordValidationMessage, true));
    });
</script>
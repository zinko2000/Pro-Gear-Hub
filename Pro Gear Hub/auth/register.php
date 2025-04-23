<?php
require_once '../config.php';
require_once 'auth_functions.php';

$error = '';
$formData = [
    'username' => '',
    'email' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['username'] = trim($_POST['username'] ?? '');
    $formData['email'] = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');
    
    // Validate inputs
    if (empty($formData['username']) || empty($formData['email']) || empty($password) || empty($confirmPassword)) {
        $error = 'All fields are required';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $result = registerUser($formData['username'], $formData['email'], $password);
        
        if ($result['status'] === 'success') {
            // Auto-login after registration
            $loginResult = loginUser($formData['username'], $password);
            if ($loginResult['status'] === 'success') {
                header('Location: ../progearhub.html');
                exit();
            }
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Pro Gear Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/auth-style.css">
</head>
<body>
    <div class="container" id="container">
        <!-- Mobile Overlay Panels -->
        <div class="mobile-overlay-panel mobile-overlay-left">
            <h1>Welcome Back!</h1>
            <p>To keep connected please login with your personal info</p>
            <button class="ghost" id="mobileSignIn" onclick="window.location.href='login.php'">Sign In</button>
        </div>
        
        <div class="mobile-overlay-panel mobile-overlay-right">
            <h1>Hello, Friend!</h1>
            <p>Enter your personal details and start your journey with us</p>
            <button class="ghost" id="mobileSignUp">Sign Up</button>
        </div>

        <!-- Registration Form -->
        <div class="form-container sign-up-container">
            <form id="registerForm" method="POST" action="register.php">
                <h1>Create Account</h1>
                <?php if ($error): ?>
                    <div class="error-message show" style="margin-bottom: 15px; text-align: center;">
                        <i class="error-icon fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>
                <div class="input-group">
                    <input type="text" id="regUsername" name="username" placeholder="Username" value="<?php echo htmlspecialchars($formData['username']); ?>">
                    <div class="error-message">
                        <i class="error-icon fas fa-exclamation-circle"></i>
                        <span id="username_error"></span>
                    </div>
                </div>
                <div class="input-group">
                    <input type="email" id="regEmail" name="email" placeholder="Email" value="<?php echo htmlspecialchars($formData['email']); ?>">
                    <div class="error-message">
                        <i class="error-icon fas fa-exclamation-circle"></i>
                        <span id="email_error"></span>
                    </div>
                </div>
                <div class="input-group">
                    <input type="password" id="regPassword" name="password" placeholder="Password">
                    <div class="error-message">
                        <i class="error-icon fas fa-exclamation-circle"></i>
                        <span id="password_error"></span>
                    </div>
                </div>
                <div class="input-group">
                    <input type="password" id="regConfirmPassword" name="confirm_password" placeholder="Confirm Password">
                    <div class="error-message">
                        <i class="error-icon fas fa-exclamation-circle"></i>
                        <span id="confirm_password_error"></span>
                    </div>
                </div>
                <button type="submit">Sign Up</button>
            </form>
        </div>

        <!-- Login Form Link -->
        <div class="form-container sign-in-container">
            <form>
                <h1>Sign In</h1>
                <p>Already have an account? Click the button below to login</p>
                <button type="button" class="ghost" onclick="window.location.href='login.php'">Sign In</button>
            </form>
        </div>

        <!-- Desktop Overlay Container -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>To keep connected please login with your personal info</p>
                    <button class="ghost" id="signIn" onclick="window.location.href='login.php'">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details and start your journey with us</p>
                    <button class="ghost" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/auth-script.js"></script>
</body>
</html>
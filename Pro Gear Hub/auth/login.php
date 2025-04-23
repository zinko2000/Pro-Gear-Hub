<?php
require_once '../config.php';
require_once 'auth_functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    $result = loginUser($username, $password);
    
    if ($result['status'] === 'success') {
        if ($result['is_admin']) {
            header('Location: ../admin/admin.php');
        } else {
            header('Location: ../progearhub.html');
        }
        exit();
    } else {
        $error = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pro Gear Hub</title>
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
            <button class="ghost" id="mobileSignIn">Sign In</button>
        </div>
        
        <div class="mobile-overlay-panel mobile-overlay-right">
            <h1>Hello, Friend!</h1>
            <p>Enter your personal details and start your journey with us</p>
            <button class="ghost" id="mobileSignUp">Sign Up</button>
        </div>

        <!-- Login Form -->
        <div class="form-container sign-in-container">
            <form id="loginForm" method="POST" action="login.php">
                <h1>Sign In</h1>
                <?php if ($error): ?>
                    <div class="error-message show" style="margin-bottom: 15px; text-align: center;">
                        <i class="error-icon fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>
                <div class="input-group">
                    <input type="text" id="loginUsername" name="username" placeholder="Username">
                    <div class="error-message">
                        <i class="error-icon fas fa-exclamation-circle"></i>
                        <span id="login_username_error"></span>
                    </div>
                </div>
                <div class="input-group">
                    <input type="password" id="loginPassword" name="password" placeholder="Password">
                    <div class="error-message">
                        <i class="error-icon fas fa-exclamation-circle"></i>
                        <span id="login_password_error"></span>
                    </div>
                </div>
                <a href="#">Forgot Your Password?</a>
                <button type="submit">Sign In</button>
            </form>
        </div>

        <!-- Registration Form Link -->
        <div class="form-container sign-up-container">
            <form>
                <h1>Create Account</h1>
                <p>To register for an account, please click the button below</p>
                <button type="button" class="ghost" onclick="window.location.href='register.php'">Sign Up</button>
            </form>
        </div>

        <!-- Desktop Overlay Container -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>To keep connected please login with your personal info</p>
                    <button class="ghost" id="signIn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details and start your journey with us</p>
                    <button class="ghost" id="signUp" onclick="window.location.href='register.php'">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/auth-script.js"></script>
</body>
</html>
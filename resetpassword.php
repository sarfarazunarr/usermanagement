<?php
session_start();
require_once 'config.php';
require_once 'sendEmail.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
        $otp = rand(100000, 999999);
        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            $stmt = $pdo->prepare("INSERT INTO otps (user_id, otp) VALUES (?, ?)");
            $stmt->execute([$user['id'], $otp]);
            
            $_SESSION['reset_email'] = $email;

            echo sendEmail($email, $username, "<p>Your OTP for reset password is: <strong>{$otp}</strong>. It will expire in 1 hour.</p>", "Verify Email");
            echo "<script>window.location.href = 'resetpassword.php?step=verify_otp';</script>";
        } else {
            echo "<script>alert('Email not found.');</script>";
        }
    } elseif (isset($_POST['otp'])) {
        $otp = $_POST['otp'];
        $email = $_SESSION['reset_email'];
        
        $stmt = $pdo->prepare("SELECT o.* FROM otps o JOIN users u ON o.user_id = u.id WHERE u.email = ? AND o.otp = ? AND o.expires_at > NOW() ORDER BY o.created_at DESC LIMIT 1");
        $stmt->execute([$email, $otp]);
        $result = $stmt->fetch();
        
        if ($result) {
            echo "<script>window.location.href = 'resetpassword.php?step=new_password';</script>";
        } else {
            echo "<script>alert('Invalid or expired OTP. Please try again.');</script>";
        }
    } elseif (isset($_POST['new_password'])) {
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $email = $_SESSION['reset_email'];
        
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$new_password, $email]);
        
        echo "<script>alert('Password updated successfully.');</script>";
        echo "<script>window.location.href = 'login.php';</script>";
    }
}

$step = isset($_GET['step']) ? $_GET['step'] : 'email';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="wrapper">
        <h2>Reset Password</h2>
    
        <?php if ($step == 'email'): ?>
        <form method="post">
            <div class="input-box">
                <input type="email" id="email" name="email" placeholder="Enter Email" required>
            </div>
            <div class="input-box button">
                <input type="submit" value="Send OTP">
            </div>
        </form>
    
        <?php elseif ($step == 'verify_otp'): ?>
        <form method="post">
            <div class="input-box">
                <input type="text" id="otp" name="otp" required placeholder="Enter OTP">
            </div>
            <div class="input-box button">
                <input type="submit" value="Verify OTP">
            </div>
        </form>
    
        <?php elseif ($step == 'new_password'): ?>
        <form method="post">
            <div class="input-box">
                <input type="password" id="new_password" name="new_password" required placeholder="New Password">
            </div>
            <div class="input-box button">
                <input type="submit" value="Update Password">
            </div>
        </form>
        <?php endif; ?>
    </div>    
    
</body>
</html>

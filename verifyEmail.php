<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email</title> 
    <link rel="stylesheet" href="style.css">
   </head>
<body>
  <div class="wrapper">
    <h2>Verify Email</h2>
    <?php
    require 'config.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user_id = $_POST['user_id'];
        $otp = $_POST['otp'];

        try {
            $stmt = $pdo->prepare("SELECT * FROM otps WHERE user_id = ? AND otp = ? AND expires_at > NOW()");
            $stmt->execute([$user_id, $otp]);
            $result = $stmt->fetch();

            if ($result) {
                $stmt = $pdo->prepare("UPDATE users SET verified = TRUE WHERE id = ?");
                $stmt->execute([$user_id]);

                $stmt = $pdo->prepare("DELETE FROM otps WHERE user_id = ?");
                $stmt->execute([$user_id]);

                echo "<p style='color: green;'>Email verified successfully!</p>";
                echo "<p>You can now <a href='login.php'>login</a> to your account.</p>";
            } else {
                echo "<p style='color: red;'>Invalid or expired OTP. Please try again.</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
    }
    ?>
    <form method="POST" action="">
      <div class="input-box">
        <input type="hidden" name="user_id" value="<?php echo isset($_GET['user_id']) ? htmlspecialchars($_GET['user_id']) : ''; ?>">
      </div>
      <div class="input-box">
        <input type="text" name="otp" placeholder="Enter OTP" required>
      </div>
      <div class="input-box button">
        <input type="submit" value="Verify OTP">
      </div>
    </form>
  </div>
</body>
</html>

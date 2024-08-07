<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title> 
    <link rel="stylesheet" href="style.css">
   </head>
<body>
  <div class="wrapper">
    <h2>Registration</h2>
    <?php
    require 'config.php';
    require 'sendEmail.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

                try {
                    $pdo->beginTransaction();

                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                    $stmt->execute([$username, $email, $password]);
                    $user_id = $pdo->lastInsertId();

                    $otp = sprintf("%06d", mt_rand(1, 999999));
                    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

                    $stmt = $pdo->prepare("INSERT INTO otps (user_id, otp, expires_at) VALUES (?, ?, ?)");
                    $stmt->execute([$user_id, $otp, $expires_at]);

                    $pdo->commit();

                    $_SESSION['user_id'] = $user_id;
                    echo sendEmail($email, $username, "<p>Your OTP for verification is: <strong>{$otp}</strong>. It will expire in 1 hour.</p>", "Verify Email");
                    echo "<p style='color: green;'>User registered successfully!</p>";
                    echo "<p>Click to <a href='verifyEmail.php?user_id={$user_id}'>verify your OTP</a> to complete registration.</p>";
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    if ($e->getCode() == '23000') {
                        echo "<p style='color: red;'>Error: Email already registered.</p>";
                    } else {
                        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
                    }
                }    }
    ?>
    <form method="POST" action="">
      <div class="input-box">
        <input type="text" name="username" placeholder="Enter your name" required>
      </div>
      <div class="input-box">
        <input type="email" name="email" placeholder="Enter your email" required>
      </div>
      <div class="input-box">
        <input type="password" name="password" placeholder="Create password" required>
      </div>
      <div class="input-box">
        <input type="password" name="confirm_password" placeholder="Confirm password" required>
      </div>
      <div class="policy">
        <input type="checkbox" required>
        <h3>I accept all terms & condition</h3>
      </div>
      <div class="input-box button">
        <input type="submit" value="Register Now">
      </div>
      <div class="text">
        <h3>Already have an account? <a href="login.php">Login now</a></h3>
      </div>
    </form>
  </div>
</body>
</html>
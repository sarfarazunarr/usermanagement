<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title> 
    <link rel="stylesheet" href="style.css">
   </head>
<body>
  <div class="wrapper">
    <h2>Login</h2>
    <?php
    require 'config.php';
    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $password = $_POST['password'];

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                echo "<p style='color: green;'>Login successful! Redirecting to profile...</p>";
                echo "<script>setTimeout(function(){ window.location.href = '/usermanagement/'; }, 2000);</script>";
            } else {
                echo "<p style='color: red;'>Error: Invalid email or password.</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
    }
    ?>
    <form method="POST" action="">
      <div class="input-box">
        <input type="email" name="email" placeholder="Enter your email" required>
      </div>
      <div class="input-box">
        <input type="password" name="password" placeholder="Enter password" required>
      </div>
      <div class="input-box button">
        <input type="submit" value="Login">
      </div>
      <div class="text">
        <h3>New Here? <a href="register.php">Register now</a></h3>
        <h3>Forgot Password? <a href="resetpassword.php">Reset now</a></h3>
      </div>
    </form>
  </div>
</body>
</html>
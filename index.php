<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title> 
    <link rel="stylesheet" href="style.css">
    <style>
      .profile-info {
        margin-bottom: 20px;
      }
      .profile-info p {
        margin: 10px 0;
      }
      .popup {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
      }
      .popup-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 400px;
      }
      .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
      }
      .close:hover,
      .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
      }
    </style>
   </head>
<body>
  <div class="wrapper">
    <h2>Profile</h2>
    <?php
    require 'config.php';
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password'])) {
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        if ($stmt->execute([$new_password, $user_id])) {
            echo "<p style='color: green;'>Password updated successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error updating password.</p>";
        }
    }
    ?>
    <div class="profile-info">
      <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    </div>
    <div class="input-box button">
      <input type="button" style="padding: 10px 5px; border-radius: 5px;" value="Update Password" onclick="document.getElementById('passwordPopup').style.display='block'">
      <input type="button" style="padding: 10px 5px; border-radius: 5px;" value="Logout" onclick="window.location.href='logout.php'">
      <input type="button" style="background: red; padding: 10px 5px; border-radius: 5px;" value="Delete Account" onclick="window.location.href='delete_account.php'">
    </div>
    

    <div id="passwordPopup" class="popup">
      <div class="popup-content">
        <span class="close" onclick="document.getElementById('passwordPopup').style.display='none'">×</span>
        <h3>Update Password</h3>
        <form method="POST" action="">
          <div class="input-box">
            <input type="password" name="new_password" placeholder="Enter new password" required>
          </div>
          <div class="input-box button">
            <input type="submit" value="Update">
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    var popup = document.getElementById('passwordPopup');
    window.onclick = function(event) {
      if (event.target == popup) {
        popup.style.display = "none";
      }
    }
  </script>
</body>
</html>
</head>
<?php

include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get the email address entered by the user
  $email = $_POST['email'];

  // Connect to the database using PDO
  $dsn = 'mysql:host=localhost;dbname=user_db';
  $name = 'user';
  $password = 'password';
  $options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
  );

  try {
    $pdo = new PDO($dsn, $name, $password, $options);

    // Check if the email address is registered in the database
    $stmt = $pdo->prepare('SELECT * FROM user_db WHERE email = :email');
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
      // Generate a password reset token and store it in the database
      $token = bin2hex(random_bytes(32));
      $expires = time() + (10 * 60);
      $stmt = $pdo->prepare('UPDATE users SET password_reset_token = :token, password_reset_expires = :expires WHERE id = :id');
      $stmt->bindParam(':token', $token);
      $stmt->bindParam(':expires', $expires);
      $stmt->bindParam(':id', $user['id']);
      $stmt->execute();

      // Send an email to the user with the password reset link
      $reset_link = "https://example.com/reset_password.php?token=$token";
      $to = $email;
      $subject = 'Reset your password';
      $message = "Hello,\n\nPlease click on the following link to reset your password:\n\n$reset_link\n\nThis link will expire in one  minutes.";
      $headers = 'From: noreply@example.com' . "\r\n" . 'Reply-To: noreply@example.com';
      mail($to, $subject, $message, $headers);

      // Show a success message to the user
      $message = "An email has been sent to $email with instructions on how to reset your password.";
      echo "<script>alert('$message');</script>";
    } else {
      // Show an error message to the user
      $message = "The email address entered is not registered in our system.";
      echo "<script>alert('$message');</script>";
    }

  } catch (PDOException $e) {
    // Show an error message if there was a problem with the database connection
    $message = "There was a problem connecting to the database: " . $e->getMessage();
    echo "<script>alert('$message');</script>";
  }
}

?>

<form method="POST">
  <label for="email">Enter your email address:</label>
  <input type="email" id="email" name="email" required>
  <button type="submit">Reset password</button>
</form>

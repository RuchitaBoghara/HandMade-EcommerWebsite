<?php
// ✅ PHP at the top
session_start();
include 'includes/db.php';

$email = $password = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST["email"]);
  $password = $_POST["password"];

  if (empty($email) || empty($password)) {
    $error = "Please fill in all fields.";
  } else {
    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
      $user = $result->fetch_assoc();
      if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        header("Location: user_profile.php");
        exit;
      } else {
        $error = "Incorrect password.";
      }
    } else {
      $error = "No account found with that email.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Login</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="js/script.js" defer></script>
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto&display=swap" rel="stylesheet">
</head>
<body>
  <header>
    <h1>🧶 Handcrafted Treasures</h1>
    <nav>
      <a href="index.php">Home</a>
      <a href="cart.php">Cart</a>
      <a href="login.php">Login</a>
      <a href="register.php">Register</a>
    </nav>
  </header>

  <main>
    <section class="hero">
      <h2>Welcome Back!</h2>
      <p>Login to continue shopping your favorite handmade treasures.</p>
    </section>

    <section class="form-section">
      <div class="form-card">
        <h3>User Login</h3>

        <?php if ($error): ?>
          <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
          <label for="email">Email</label>
          <input type="email" name="email" required value="<?= htmlspecialchars($email) ?>">

          <label for="password">Password</label>
          <div class="password-wrapper">
            <input type="password" name="password" id="login-password" required>
            <span class="toggle-password" onclick="togglePassword('login-password')">👁️</span>
          </div>

          <button type="submit" class="btn">Login</button>
        </form>
      </div>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 Handcrafted Treasures | Made with 💛</p>
  </footer>
</body>
</html>

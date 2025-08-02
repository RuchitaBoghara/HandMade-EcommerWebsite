<?php
include 'includes/db.php';
$name = $email = $password = $confirm = "";
$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST["name"]);
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm  = $_POST["confirm"];

    if (empty($name) || empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed);
        if ($stmt->execute()) {
            $success = "Registration successful! You can now <a href='login.php'>login</a>.";
        } else {
            $error = "Email already exists or error occurred.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Registration</title>
  <link rel="stylesheet" href="css/style.css">
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

   <!-- Inside <main> -->
<main>
  <section class="hero">
    <h2>Join Our Handmade Community</h2>
    <p>Register now to save your favorites and shop faster!</p>
  </section>

  <section class="form-section">
    <div class="form-card">
      <h3>Create Your Account</h3>

      <?php if ($success): ?>
        <p class="success"><?= $success ?></p>
      <?php elseif ($error): ?>
        <p class="error"><?= $error ?></p>
      <?php endif; ?>

      <form method="POST">
        <label for="name">Full Name</label>
        <input type="text" name="name" required value="<?= htmlspecialchars($name) ?>">

        <label for="email">Email</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($email) ?>">

        <label for="password">Password</label>
        <input type="password" name="password" required>

        <label for="confirm">Confirm Password</label>
        <input type="password" name="confirm" required>

        <button type="submit" class="btn">Register</button>
      </form>
    </div>
  </section>
</main>

  </main>

  <footer>
    <p>&copy; 2025 Handcrafted Treasures | Made with 💛</p>
  </footer>

</body>
</html>

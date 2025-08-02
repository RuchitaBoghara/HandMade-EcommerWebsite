<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Handcrafted Treasures</title>
  <link rel="stylesheet" href="css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Roboto&display=swap" rel="stylesheet">
</head>
<body>

  <header>
    <h1>🧶 Handcrafted Treasures</h1>
    <?php session_start(); ?>
    <nav>
  <a href="index.php">Home</a>
  <a href="store.php">Store</a>
  <a href="cart.php">Cart</a>
  <?php if (isset($_SESSION['user_id'])): ?>
      <a href="user_profile.php" title="View Profile">
        👤 <?= htmlspecialchars($_SESSION['user_name']) ?>
      </a>
      <a href="logout.php">Logout</a>
  <?php else: ?>
      <a href="login.php">Login</a>
      <a href="register.php">Register</a>
  <?php endif; ?>
</nav>



  </header>

  <main>
    <section class="hero">
      <h2>Where Every Item Tells a Story</h2>
      <p>Explore our curated collection of handmade products from local artisans.</p>
    </section>

    <section class="product-grid">
      <?php
        $result = $conn->query("SELECT * FROM products");
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "
              <div class='product-card'>
                <img src='images/{$row['image']}' alt='{$row['name']}'>
                <h3>{$row['name']}</h3>
                <p class='price'>₹{$row['price']}</p>
                <a href='product.php?id={$row['id']}' class='btn'>View Product</a>
              </div>
            ";
          }
        } else {
          echo "<p>No products found.</p>";
        }
      ?>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 Handcrafted Treasures | Made with 💛 by Artisans</p>
  </footer>

</body>
</html>

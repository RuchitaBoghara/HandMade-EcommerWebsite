<?php
session_start();
include 'includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: store.php');
    exit();
}

$product_id = (int)$_GET['id'];
$result = $conn->query("SELECT * FROM products WHERE id = $product_id");

if ($result->num_rows === 0) {
    echo "Product not found.";
    exit();
}

$product = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($product['name']) ?> - Handcrafted Product</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    html, body {
  height: 100%;
  margin: 0;
  font-family: 'Roboto', sans-serif;
  background-color: #f9f9f9;
  display: flex;
  flex-direction: column;
}

header {
  background-color: #333;
  color: white;
  padding: 15px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

header h1 {
  font-size: 24px;
  margin: 0;
}

nav a {
  color: white;
  text-decoration: none;
  margin-left: 20px;
  font-weight: 500;
}

.product-container {
  flex: 1; /* pushes footer down */
  width: 80%;
  max-width: 1000px;  /* limit on large screens */
  min-width: 600px;   /* prevent skinny card */
  margin: 40px auto;
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  display: flex;
  gap: 30px;
  padding: 30px;
  align-items:center;
}

.product-image {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: flex-start;
}

.product-image img {
  width: 100%;
  max-width: 350px;
  border-radius: 8px;
  object-fit: cover;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.product-details {
  flex: 2;
}

.product-details h2 {
  margin-top: 0;
  font-size: 28px;
  color: #333;
}

.product-details p.description {
  font-size: 16px;
  margin: 20px 0;
  color: #555;
  line-height: 1.6;
}

.product-details .price {
  font-size: 24px;
  font-weight: bold;
  color: #0077cc;
  margin: 15px 0;
}

.product-details .stock {
  color: green;
  margin-bottom: 20px;
  font-weight: 500;
}

.product-details button {
  padding: 10px 20px;
  background-color: #0077cc;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
  transition: background 0.3s;
}

.product-details button:hover {
  background-color: #005fa3;
}

footer {
  text-align: center;
  padding: 20px;
  background-color: #f2f2f2;
  margin-top: auto; /* footer stays at bottom */
  color: #777;
  font-size: 14px;
}

.back-btn {
  display: inline-block;
  padding: 10px 20px;
  background-color: #6c757d;
  color: white;
  text-decoration: none;
  border-radius: 6px;
  font-size: 16px;
  transition: background 0.3s;
}

.back-btn:hover {
  background-color: #5a6268;
}


  </style>
</head>
<body>

<header>
  <h1>Handcrafted Treasures</h1>
  <nav>
    <a href="index.php">Home</a>
    <a href="store.php">Store</a>
    <a href="cart.php">Cart</a>
    <a href="user_profile.php">👤 <?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Guest' ?></a>
  </nav>
</header>

<div class="product-container">
  <!-- Image LEFT -->
  <div class="product-image">
    <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
  </div>

  <!-- Details RIGHT -->
  <div class="product-details">
    <h2><?= htmlspecialchars($product['name']) ?></h2>
    <p class="description"><?= htmlspecialchars($product['description']) ?></p>
    <p class="price">₹<?= number_format($product['price'], 2) ?></p>
    <p class="stock"><?= $product['stock'] > 0 ? $product['stock'] . ' in stock' : 'Out of stock' ?></p>

    <form action="add_to_cart.php" method="post" style="margin-bottom:15px;">
      <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
      <button type="submit" <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>🛒 Add to Cart</button>
      <a style="margin-left:30px;" href="store.php" class="back-btn">⬅ Back to Store</a>
    </form>

    <!-- ✅ Back Button -->
    
  </div>
</div>


<footer>
  <p>&copy; 2025 Handcrafted Treasures | Made with 💛 by Artisans</p>
</footer>

</body>
</html>

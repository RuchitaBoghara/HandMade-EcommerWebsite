<?php
session_start();
include 'includes/db.php';

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$price_filter = isset($_GET['price']) ? $_GET['price'] : '';

$query = "SELECT * FROM products WHERE 1";

if (!empty($search)) {
    $query .= " AND (name LIKE '%$search%' OR description LIKE '%$search%')";
}

if ($price_filter === 'low') {
    $query .= " AND price <= 500";
} elseif ($price_filter === 'mid') {
    $query .= " AND price > 500 AND price <= 1000";
} elseif ($price_filter === 'high') {
    $query .= " AND price > 1000";
}

if ($sort === 'price_asc') {
    $query .= " ORDER BY price ASC";
} elseif ($sort === 'price_desc') {
    $query .= " ORDER BY price DESC";
} elseif ($sort === 'name') {
    $query .= " ORDER BY name ASC";
} else {
    $query .= " ORDER BY id DESC";
}

$products = $conn->query($query);
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Store - Handcrafted Products</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body { font-family: 'Roboto', sans-serif; margin: 0; background: #f2f2f2; }
    header { background: #222; color: #fff; padding: 20px 30px; display: flex; justify-content: space-between; }
    header h1 { margin: 0; }
    nav a { color: white; text-decoration: none; margin-left: 20px; }

    .store-container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
    .filter-bar { display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 25px; }
    .filter-bar input, .filter-bar select {
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 16px;
      min-width: 180px;
    }

    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 25px;
    }

    .product-card {
      background: white;
      border-radius: 10px;
      padding: 15px;
      text-align: center;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      transition: transform 0.2s;
    }
    .product-card:hover { transform: translateY(-5px); }
    .product-card img {
      width: 100%; height: 180px; object-fit: cover; border-radius: 6px;
    }
    .product-card h3 { margin: 10px 0 5px; font-size: 18px; color: #333; }
    .product-card p { margin: 5px 0; color: #555; }
    .product-card a {
      display: inline-block;
      margin-top: 10px;
      padding: 8px 14px;
      background: #0077cc;
      color: white;
      text-decoration: none;
      border-radius: 6px;
    }

    footer { text-align: center; padding: 20px; margin-top: 40px; color: #777; background: #eee; }
  </style>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const inputs = document.querySelectorAll('.filter-bar input, .filter-bar select');
      inputs.forEach(input => {
        input.addEventListener('change', () => {
          const form = document.getElementById('filter-form');
          form.submit();
        });
      });
    });
  </script>
</head>
<body>

<header>
  <h1>🛍️ Handmade Store</h1>
  <nav>
    <a href="index.php">Home</a>
    <a href="store.php">Store</a>
    <a href="cart.php">Cart</a>
    <a href="user_profile.php">👤 <?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Guest' ?></a>
  </nav>
</header>

<div class="store-container">
  <form method="GET" class="filter-bar" id="filter-form">
    <input type="text" name="search" placeholder="🔍 Search..." value="<?= htmlspecialchars($search) ?>">

    <select name="price">
      <option value="">💰 Price</option>
      <option value="low" <?= $price_filter === 'low' ? 'selected' : '' ?>>Under ₹500</option>
      <option value="mid" <?= $price_filter === 'mid' ? 'selected' : '' ?>>₹501–₹1000</option>
      <option value="high" <?= $price_filter === 'high' ? 'selected' : '' ?>>Above ₹1000</option>
    </select>

    <select name="sort">
      <option value="">🕒 Sort</option>
      <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price ↑</option>
      <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price ↓</option>
      <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name A-Z</option>
    </select>
  </form>

  <div class="product-grid">
    <?php if ($products->num_rows > 0): ?>
      <?php while ($product = $products->fetch_assoc()): ?>
        <div class="product-card">
          <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
          <h3><?= htmlspecialchars($product['name']) ?></h3>
          <p>₹<?= number_format($product['price'], 2) ?></p>
          <a href="product.php?id=<?= $product['id'] ?>">View</a>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No products found.</p>
    <?php endif; ?>
  </div>
</div>

<footer>
  <p>&copy; 2025 Handcrafted Treasures | Made with 💛 by Artisans</p>
</footer>

</body>
</html>

<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];
$product_ids = array_keys($cart);
$products = [];

if (count($product_ids) > 0) {
  $ids = implode(',', array_map('intval', $product_ids));
  $query = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
  while ($row = $query->fetch_assoc()) {
    $row['quantity'] = $cart[$row['id']];
    $products[] = $row;
  }
}

// Handle remove
if (isset($_GET['remove'])) {
  $id = intval($_GET['remove']);
  unset($_SESSION['cart'][$id]);
  header("Location: cart.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shopping Cart</title>
  <style>
    body { font-family: 'Roboto', sans-serif; margin: 0; background: #f2f2f2; }
    header { background: #222; color: white; padding: 20px 30px; display: flex; justify-content: space-between; }
    nav a { color: white; text-decoration: none; margin-left: 20px; }

    .cart-container { max-width: 1000px; margin: 40px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    h2 { text-align: center; color: #333; }

    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 12px 15px; text-align: center; }
    th { background-color: #0077cc; color: white; }
    tr:nth-child(even) { background-color: #f9f9f9; }

    img { width: 80px; height: 80px; object-fit: cover; border-radius: 6px; }
    input[type="number"] {
      width: 60px; padding: 6px; border-radius: 4px; border: 1px solid #ccc;
    }
    .remove-btn {
      background: red; color: white; padding: 6px 10px; border: none; border-radius: 4px; cursor: pointer;
    }
    .checkout-btn {
      padding: 10px 18px; background: #0077cc; color: white; border: none; border-radius: 6px; cursor: pointer;
    }
    .total-box { text-align: right; margin-top: 20px; font-size: 18px; }
    footer { text-align: center; margin-top: 40px; padding: 20px; background: #eee; color: #777; }
  </style>
</head>
<body>
<header>
  <h1>Your Cart</h1>
  <nav>
    <a href="index.php">Home</a>
    <a href="store.php">Store</a>
    <a href="user_profile.php">👤 <?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Guest' ?></a>
  </nav>
</header>

<div class="cart-container">
  <h2>🛒 Shopping Cart</h2>

  <?php if (count($products) > 0): ?>
    <form method="post">
      <table>
        <tr>
          <th>Product</th>
          <th>Name</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Subtotal</th>
          <th>Remove</th>
        </tr>
        <?php foreach ($products as $product): ?>
          <tr data-id="<?= $product['id'] ?>" data-price="<?= $product['price'] ?>">
            <td><img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"></td>
            <td><?= htmlspecialchars($product['name']) ?></td>
            <td>₹<?= number_format($product['price'], 2) ?></td>
            <td><input class="qty" type="number" value="<?= $product['quantity'] ?>" min="1" data-id="<?= $product['id'] ?>"></td>
            <td class="subtotal">₹<?= number_format($product['price'] * $product['quantity'], 2) ?></td>
            <td><a class="remove-btn" href="cart.php?remove=<?= $product['id'] ?>">X</a></td>
          </tr>
        <?php endforeach; ?>
      </table>

      <div class="total-box">
        <p><strong>Total: ₹<span id="total"><?= number_format(array_sum(array_map(function ($p) {
          return $p['price'] * $p['quantity'];
        }, $products)), 2) ?></span></strong></p>
      </div>

      <div style="text-align:right; margin-top:20px;">
        <a href="checkout.php" class="checkout-btn" style="text-decoration:none; display:inline-block;">Proceed to Checkout</a>
        </div>

    </form>
  <?php else: ?>
    <p style="text-align:center; color: #666;">Your cart is empty. <a href="store.php">Browse products</a>.</p>
  <?php endif; ?>
</div>

<footer>
  <p>&copy; 2025 Handcrafted Treasures | Made with 💛 by Artisans</p>
</footer>

<script>
  document.querySelectorAll('.qty').forEach(input => {
    input.addEventListener('input', function () {
      const row = this.closest('tr');
      const price = parseFloat(row.getAttribute('data-price'));
      const qty = Math.max(1, parseInt(this.value) || 1);
      const subtotal = price * qty;

      // Update subtotal in UI
      row.querySelector('.subtotal').innerText = '₹' + subtotal.toFixed(2);

      // Update total
      let total = 0;
      document.querySelectorAll('tr[data-id]').forEach(r => {
        const p = parseFloat(r.getAttribute('data-price'));
        const q = parseInt(r.querySelector('.qty').value) || 1;
        total += p * q;
      });
      document.getElementById('total').innerText = total.toFixed(2);
    });
  });
</script>
</body>
</html>

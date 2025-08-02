<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'];
$product_ids = array_keys($cart);
$products = [];

$total = 0;
if (!empty($product_ids)) {
    $ids = implode(",", array_map('intval', $product_ids));
    $query = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
    while ($row = $query->fetch_assoc()) {
        $row['quantity'] = $cart[$row['id']];
        $products[] = $row;
        $total += $row['price'] * $row['quantity'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $conn->real_escape_string($_POST['address'] . ", " . $_POST['city'] . ", " . $_POST['state'] . " - " . $_POST['pincode']);
    $payment = $conn->real_escape_string($_POST['payment_method']);

    $conn->query("INSERT INTO orders (user_id, address, payment_method, total, order_date) VALUES ($user_id, '$address', '$payment', $total, NOW())");

    $_SESSION['cart'] = [];
    header('Location: user_profile.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout</title>
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      margin: 0;
      background: #f2f2f2;
    }
    header {
      background: #333;
      color: white;
      padding: 20px 30px;
      display: flex;
      justify-content: space-between;
    }
    nav a {
      color: white;
      text-decoration: none;
      margin-left: 20px;
    }
    .checkout-container {
      max-width: 800px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h2 {
      margin-bottom: 20px;
      color: #333;
      text-align: center;
    }
    .tabs {
      display: flex;
      margin-bottom: 20px;
      border-bottom: 2px solid #ddd;
    }
    .tab {
      flex: 1;
      padding: 12px;
      text-align: center;
      cursor: pointer;
      background: #eee;
      border-right: 1px solid #ddd;
    }
    .tab.active {
      background: #fff;
      font-weight: bold;
      border-bottom: 2px solid #0077cc;
    }
    .tab-content {
      display: none;
    }
    .tab-content.active {
      display: block;
    }
    input, textarea, select {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      margin-bottom: 15px;
    }
    .btn {
      padding: 12px 20px;
      border: none;
      background-color: #0077cc;
      color: white;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s;
    }
    .btn:hover {
      background-color: #005fa3;
    }
    .back-btn {
      background-color: #aaa;
      margin-right: 10px;
    }
    #qr-image {
      width: 200px;
      display: none;
      margin-bottom: 15px;
    }
    #card-fields {
  display: none;
  margin-top: 15px;
}

    footer {
      text-align: center;
      margin-top: 50px;
      padding: 20px;
      background: #eee;
      color: #777;
    }
  </style>
</head>
<body>

<header>
  <h1>Checkout</h1>
  <nav>
    <a href="store.php">Store</a>
    <a href="cart.php">Cart</a>
    <a href="user_profile.php">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></a>
  </nav>
</header>

<div class="checkout-container">
  <h2>🧾 Complete Your Order</h2>

  <div class="tabs">
    <div class="tab active" onclick="openTab(0)">📦 Address</div>
    <div class="tab" onclick="openTab(1)">💳 Payment</div>
  </div>

  <form method="post" id="checkoutForm">
  <!-- Address Tab -->
  <div class="tab-content active" id="address-tab">
    <label>Street Address:</label>
    <input type="text" name="address" required>
    <label>City:</label>
    <input type="text" name="city" required>
    <label>State:</label>
    <input type="text" name="state" required>
    <label>Pincode:</label>
    <input type="text" name="pincode" required pattern="\d{6}" title="Enter valid 6-digit pincode">
    
    <div style="text-align:right;">
      <button type="button" class="btn" onclick="goToPaymentTab()">Next →</button>
    </div>
  </div>

  <!-- Payment Tab -->
  <div class="tab-content" id="payment-tab">
    <label>Select Payment Method:</label>
    <select name="payment_method" required onchange="togglePaymentFields(this.value)">
      <option value="">-- Choose --</option>
      <option value="COD">Cash on Delivery</option>
      <option value="UPI">UPI</option>
      <option value="Card">Credit/Debit Card</option>
    </select>

    <img id="qr-image" src="images/upi-qr.png" alt="UPI QR Code" style="display:none;">

    <div class="card-details" id="card-fields">
      <input type="text" name="card_number" placeholder="Card Number">
      <input type="text" name="card_holder" placeholder="Card Holder Name">
      <input type="text" name="expiry" placeholder="Expiry (MM/YY)">
      <input type="text" name="cvv" placeholder="CVV">
    </div>

    <div style="text-align: right;">
      <a href="cart.php" class="btn back-btn">← Back to Cart</a>
      <button type="submit" class="btn" onclick="return confirmAddress()">✅ Place Order</button>
    </div>
  </div>
</form>

</div>

<footer>
  <p>&copy; 2025 Handcrafted Treasures | Made with 💛 by Artisans</p>
</footer>

<script>
function goToPaymentTab() {
  const fields = ['address', 'city', 'state', 'pincode'];
  for (const name of fields) {
    const input = document.querySelector(`[name="${name}"]`);
    if (!input.value.trim()) {
      alert(`Please fill in the ${name} field.`);
      input.focus();
      return;
    }
  }
  openTab(1);
}

function openTab(index) {
  const tabs = document.querySelectorAll('.tab');
  const contents = document.querySelectorAll('.tab-content');
  tabs.forEach((tab, i) => {
    tab.classList.toggle('active', i === index);
    contents[i].classList.toggle('active', i === index);
  });
}

function togglePaymentFields(method) {
  document.getElementById('qr-image').style.display = method === 'UPI' ? 'block' : 'none';
  document.getElementById('card-fields').style.display = method === 'Card' ? 'block' : 'none';
}

function confirmAddress() {
  const address = document.querySelector('[name="address"]').value;
  const city = document.querySelector('[name="city"]').value;
  const state = document.querySelector('[name="state"]').value;
  const pincode = document.querySelector('[name="pincode"]').value;
  return confirm(`Please confirm your address:\n\n${address}, ${city}, ${state} - ${pincode}`);
}
</script>
</body>
</html>


</body>
</html>

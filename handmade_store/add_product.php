<?php
include 'admin_check.php';
include 'includes/db.php';

$name = $description = $image = $price = $stock = "";
$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $image = trim($_POST['image']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    if ($name && $price > 0 && $stock >= 0) {
        $stmt = $conn->prepare("INSERT INTO products (name, description, image, price, stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdi", $name, $description, $image, $price, $stock);
        if ($stmt->execute()) {
            $success = "Product added successfully.";
        } else {
            $error = "Failed to add product.";
        }
    } else {
        $error = "Please fill in all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Add Product</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <header>
    <h1>➕ Add Product</h1>
    <nav>
      <a href="admin_dashboard.php">Dashboard</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main>
    <section class="form-section">
      <div class="form-card">
        <h3>New Product</h3>
        <?php if ($success): ?><p class="success"><?= $success ?></p><?php endif; ?>
        <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>

        <form method="POST">
          <label>Name</label>
          <input type="text" name="name" required>

          <label>Description</label>
          <input type="text" name="description">

          <label>Image Filename (e.g. product.jpg)</label>
          <input type="text" name="image">

          <label>Price (₹)</label>
          <input type="number" name="price" step="0.01" required>

          <label>Stock</label>
          <input type="number" name="stock" required>

          <button type="submit" class="btn">Add Product</button>
        </form>
      </div>
    </section>
  </main>

</body>
</html>

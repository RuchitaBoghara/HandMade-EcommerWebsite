<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_query = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $user_query->fetch_assoc();
$isAdmin = (isset($user['role']) && $user['role'] === 'admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $filename = basename($_FILES["profile_pic"]["name"]);
        $target_file = $target_dir . time() . "_" . $filename;

        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            $conn->query("UPDATE users SET profile_pic = '$target_file' WHERE id = $user_id");
            $user['profile_pic'] = $target_file;
        }
    }

    if (isset($_POST['update_profile'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $conn->query("UPDATE users SET name = '$name', email = '$email' WHERE id = $user_id");
        $_SESSION['user_name'] = $name;
        $user['name'] = $name;
        $user['email'] = $email;
    }
}

$profile_image = (!empty($user['profile_pic']) && file_exists($user['profile_pic'])) ? $user['profile_pic'] : 'images/default_user.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Profile</title>
  <link rel="stylesheet" href="css/style.css">
  <script>
    // Tab switching function
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        
        // Hide all tab content
        tabcontent = document.getElementsByClassName("tab-panel");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("active");
        }
        
        // Remove active class from all tab buttons
        tablinks = document.getElementsByClassName("tab-button");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
        }
        
        // Show the selected tab and mark button as active
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.classList.add("active");
    }

    // Profile edit functions
    function showEditForm() {
        document.getElementById('profile-view').style.display = 'none';
        document.getElementById('profile-edit').style.display = 'block';
    }

    function hideEditForm() {
        document.getElementById('profile-view').style.display = 'block';
        document.getElementById('profile-edit').style.display = 'none';
    }
  </script>
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      margin: 0;
      background-color: #f9f9f9;
    }
    header {
      background-color: #333;
      color: white;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    header h1 { font-size: 24px; }
    nav a {
      color: white;
      text-decoration: none;
      margin-left: 20px;
      font-weight: 500;
    }
    .profile-wrapper {
      max-width: 1200px;
      margin: 30px auto;
      display: flex;
      background: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .tabs-container { display: flex; width: 100%; }
    .tab-links {
      width: 220px;
      background-color: #f4f4f4;
      display: flex;
      flex-direction: column;
      border-right: 1px solid #ccc;
    }
    .tab-button {
      padding: 15px;
      background: none;
      border: none;
      text-align: left;
      cursor: pointer;
      font-size: 16px;
      border-bottom: 1px solid #ddd;
      transition: background 0.3s;
    }
    .tab-button:hover, .tab-button.active {
      background-color: #e0e0e0;
    }
    .tab-content { flex: 1; padding: 30px; }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 15px;
    }
    .product-card {
      border: 1px solid #ddd;
      padding: 10px;
      border-radius: 8px;
      text-align: center;
      background: #fafafa;
    }
    .product-card img {
      width: 100%;
      height: 130px;
      object-fit: cover;
      border-radius: 4px;
    }

    form input[type="text"],
    form input[type="email"],
    form input[type="file"] {
      width: 100%;
      padding: 10px;
      margin-top: 8px;
      border: 1px solid #ccc;
      border-radius: 6px;
      box-sizing: border-box;
    }
    
    form label {
      display: block;
      margin-top: 15px;
      font-weight: bold;
    }
    
    form button, .edit-btn {
      padding: 10px 15px;
      background-color: #0077cc;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 10px;
      margin-right: 10px;
    }
    
    form button:hover, .edit-btn:hover {
      background-color: #005fa3;
    }
    
    .btn {
      display: inline-block;
      padding: 8px 12px;
      background-color: #0077cc;
      color: white;
      text-decoration: none;
      border-radius: 4px;
      margin-top: 8px;
    }
    
    .btn:hover {
      background-color: #005fa3;
    }

    h2 {
      border-bottom: 2px solid #eee;
      padding-bottom: 6px;
      margin-bottom: 20px;
      font-size: 22px;
      color: #333;
    }

    .order-list {
      list-style: none;
      padding: 0;
    }

    .order-list li {
      background: #f5f5f5;
      margin-bottom: 10px;
      padding: 12px;
      border-left: 4px solid #0077cc;
      border-radius: 6px;
    }
    
    footer {
      text-align: center;
      padding: 20px;
      background-color: #333;
      color: white;
      margin-top: 30px;
    }
    .product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
  padding-top: 20px;
}

.product-card {
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 12px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  padding: 15px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.product-card img {
  width: 100%;
  height: 160px;
  object-fit: cover;
  border-radius: 8px;
  margin-bottom: 10px;
}

.product-card form {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.product-card input {
  padding: 8px;
  font-size: 14px;
  border-radius: 6px;
  border: 1px solid #ccc;
}

.product-card label {
  font-weight: bold;
  margin-top: 5px;
}

.product-card button {
  padding: 8px 12px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.product-card button:hover {
  background-color: #0056b3;
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
    <a href="user_profile.php">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main class="profile-wrapper">
  <div class="tabs-container">
    <div class="tab-links">
      <button class="tab-button active" onclick="openTab(event, 'profile')">👤 Profile</button>
      <button class="tab-button" onclick="openTab(event, 'liked')">❤️ Liked Products</button>
      <button class="tab-button" onclick="openTab(event, 'orders')">🛒 Order History</button>
      <?php if ($isAdmin): ?>
        <button class="tab-button" onclick="openTab(event, 'admin')">💼 Admin Panel</button>
      <?php endif; ?>
    </div>

    <div class="tab-content">
      <!-- Profile Tab -->
      <div id="profile" class="tab-panel active">
        <h2>👤 My Profile</h2>

        <!-- VIEW MODE -->
        <div id="profile-view">
            <img src="<?= $profile_image ?>" alt="Profile Photo" style="width:130px; height:130px; border-radius:50%; object-fit:cover; margin-bottom:10px;">
            <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <button type="button" class="edit-btn" onclick="showEditForm()">✏️ Edit Profile</button>
        </div>

        <!-- EDIT MODE -->
        <div id="profile-edit" style="display:none;">
            <form method="post" enctype="multipart/form-data">
            <label for="profile_pic">Profile Photo</label>
            <input type="file" name="profile_pic" id="profile_pic" accept="image/*">
            
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>" required>
            
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            
            <div style="margin-top: 20px;">
                <button type="submit" name="update_profile">💾 Save Changes</button>
                <button type="button" onclick="hideEditForm()">❌ Cancel</button>
            </div>
            </form>
        </div>
      </div>

      <!-- Liked Products -->
      <div id="liked" class="tab-panel">
        <h2>❤️ Liked Products</h2>
        <div class="product-grid">
          <?php
          $likes = $conn->query("SELECT p.* FROM likes l JOIN products p ON l.product_id = p.id WHERE l.user_id = $user_id");
          if ($likes && $likes->num_rows > 0) {
              while ($product = $likes->fetch_assoc()) {
                  echo "
                    <div class='product-card'>
                      <img src='images/{$product['image']}' alt='" . htmlspecialchars($product['name']) . "'>
                      <h3>" . htmlspecialchars($product['name']) . "</h3>
                      <p>₹{$product['price']}</p>
                      <a href='product.php?id={$product['id']}' class='btn'>View</a>
                    </div>
                  ";
              }
          } else {
              echo "<p>No liked products found.</p>";
          }
          ?>
        </div>
      </div>

      <!-- Order History -->
      <div id="orders" class="tab-panel">
        <h2>🛒 Order History</h2>
        <ul class="order-list">
          <?php
          $orders = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY order_date DESC");
          if ($orders && $orders->num_rows > 0) {
              while ($order = $orders->fetch_assoc()) {
                  echo "<li><strong>Order #{$order['id']}</strong> — ₹{$order['total']} — {$order['order_date']}</li>";
              }
          } else {
              echo "<li>No orders found.</li>";
          }
          ?>
        </ul>
      </div>
    </div>
    <?php if ($isAdmin): ?>
<div id="admin" class="tab-panel">
  <h2>💼 Admin Product Management</h2>
  <button onclick="toggleAddForm()" class="edit-btn" style="margin-bottom:20px;">➕ Add New Product</button>

  <!-- Add Product Form -->
  <form method="post" id="addForm" style="display:none; margin-bottom: 30px; background:#f9f9f9; padding:20px; border-radius:10px;">
    <h3>Add New Product</h3>
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:15px;">
      <div>
        <label>Name</label>
        <input type="text" name="pname" required>
      </div>
      <div>
        <label>Description</label>
        <input type="text" name="pdesc">
      </div>
      <div>
        <label>Image Filename (in /images folder)</label>
        <input type="text" name="pimage">
      </div>
      <div>
        <label>Price (₹)</label>
        <input type="number" step="0.01" name="pprice" required>
      </div>
      <div>
        <label>Stock</label>
        <input type="number" name="pstock" required>
      </div>
    </div>
    <button type="submit" name="add_product" class="edit-btn" style="margin-top:15px;">✅ Add Product</button>
  </form>

  <!-- Product Management Grid -->
  <div class="admin-product-grid">
    <?php
    $allProducts = $conn->query("SELECT * FROM products ORDER BY id DESC");
    if ($allProducts && $allProducts->num_rows > 0) {
        while ($prod = $allProducts->fetch_assoc()):
    ?>
    <div class="admin-product-card">
      <div class="product-image-container">
        <img src="images/<?= htmlspecialchars($prod['image']) ?>" 
             alt="<?= htmlspecialchars($prod['name']) ?>" 
             onerror="this.src='images/default_product.png'">
        <div class="product-id">ID: <?= $prod['id'] ?></div>
      </div>
      
      <form method="post" class="product-form">
        <input type="hidden" name="prod_id" value="<?= $prod['id'] ?>">

        <div class="form-group">
          <label>Product Name</label>
          <input type="text" name="name" value="<?= htmlspecialchars($prod['name']) ?>" required>
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea name="description" rows="2"><?= htmlspecialchars($prod['description']) ?></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Price (₹)</label>
            <input type="number" name="price" value="<?= $prod['price'] ?>" step="0.01" required>
          </div>
          <div class="form-group">
            <label>Stock</label>
            <input type="number" name="stock" value="<?= $prod['stock'] ?>" required>
          </div>
        </div>

        <div class="form-group">
          <label>Image Filename</label>
          <input type="text" name="image" value="<?= htmlspecialchars($prod['image']) ?>">
        </div>

        <div class="button-group">
          <button type="submit" name="update_product" class="update-btn">💾 Update</button>
          <button type="submit" name="delete_product" class="delete-btn" 
                  onclick="return confirm('Are you sure you want to delete this product?')">🗑️ Delete</button>
        </div>
      </form>
    </div>
    <?php 
        endwhile;
    } else {
        echo "<p style='grid-column: 1 / -1; text-align: center; color: #666;'>No products found.</p>";
    }
    ?>
  </div>
</div>

<script>
function toggleAddForm() {
  const form = document.getElementById('addForm');
  form.style.display = (form.style.display === 'none') ? 'block' : 'none';
}
</script>

<style>
/* Admin Product Grid Styles */
.admin-product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 25px;
  margin-top: 20px;
  width: 100%;
}

.admin-product-card {
  background: white;
  border: 1px solid #ddd;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  overflow: hidden;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  min-height: 500px;
  display: flex;
  flex-direction: column;
}

.admin-product-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.product-image-container {
  position: relative;
  height: 180px;
  overflow: hidden;
}

.product-image-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-id {
  position: absolute;
  top: 8px;
  right: 8px;
  background: rgba(0,0,0,0.7);
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: bold;
}

.product-form {
  padding: 20px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.form-group {
  margin-bottom: 15px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.form-group label {
  display: block;
  font-weight: bold;
  margin-bottom: 5px;
  color: #333;
  font-size: 13px;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 8px 10px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 14px;
  box-sizing: border-box;
  transition: border-color 0.2s ease;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #0077cc;
  box-shadow: 0 0 0 2px rgba(0,119,204,0.1);
}

.form-group textarea {
  resize: vertical;
  min-height: 60px;
}

.button-group {
  display: flex;
  gap: 10px;
  margin-top: auto;
  padding-top: 15px;
}

.update-btn {
  flex: 1;
  padding: 10px 15px;
  background-color: #28a745;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.2s ease;
}

.update-btn:hover {
  background-color: #218838;
}

.delete-btn {
  flex: 1;
  padding: 10px 15px;
  background-color: #dc3545;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.2s ease;
}

.delete-btn:hover {
  background-color: #c82333;
}

/* Responsive adjustments */
@media (max-width: 900px) {
  .admin-product-grid {
    grid-template-columns: 1fr;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .button-group {
    flex-direction: column;
  }
}

@media (min-width: 901px) and (max-width: 1200px) {
  .admin-product-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1201px) {
  .admin-product-grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

/* Add form improvements */
#addForm {
  border: 2px solid #0077cc;
  background: linear-gradient(135deg, #f8f9ff 0%, #e8f4ff 100%);
}

#addForm h3 {
  margin-top: 0;
  color: #0077cc;
  text-align: center;
}

#addForm .edit-btn {
  width: 100%;
  background: linear-gradient(135deg, #0077cc 0%, #005fa3 100%);
  font-size: 16px;
  padding: 12px;
}

#addForm .edit-btn:hover {
  background: linear-gradient(135deg, #005fa3 0%, #004080 100%);
}
</style>

<?php endif; ?>   

  </div>
</div>



</main>

<footer>
  <p>&copy; 2025 Handcrafted Treasures | Made with 💛 by Artisans</p>
</footer>
<?php
// Handle admin form submission
if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        $pname = $conn->real_escape_string($_POST['pname']);
        $pdesc = $conn->real_escape_string($_POST['pdesc']);
        $pimage = $conn->real_escape_string($_POST['pimage']);
        $pprice = floatval($_POST['pprice']);
        $pstock = intval($_POST['pstock']);
        $conn->query("INSERT INTO products (name, description, image, price, stock) VALUES ('$pname', '$pdesc', '$pimage', $pprice, $pstock)");
    }

    if (isset($_POST['update_product'])) {
        $id = intval($_POST['prod_id']);
        $name = $conn->real_escape_string($_POST['name']);
        $desc = $conn->real_escape_string($_POST['description']);
        $image = $conn->real_escape_string($_POST['image']);
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);
        $conn->query("UPDATE products SET name='$name', description='$desc', image='$image', price=$price, stock=$stock WHERE id=$id");
    }

    if (isset($_POST['delete_product'])) {
        $id = intval($_POST['prod_id']);
        $conn->query("DELETE FROM products WHERE id=$id");
    }
    header("Location: user_profile.php");
    exit();
}
?>

<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])): ?>
<script>
// Auto-hide edit form after successful submission
setTimeout(function() {
    hideEditForm();
}, 500);
</script>
<?php endif; ?>

</body>
</html>
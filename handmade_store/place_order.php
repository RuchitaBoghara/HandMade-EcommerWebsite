<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $name = $conn->real_escape_string($_POST['name']);
    $address = $conn->real_escape_string($_POST['address']);
    $city = $conn->real_escape_string($_POST['city']);
    $state = $conn->real_escape_string($_POST['state']);
    $zip = $conn->real_escape_string($_POST['zip']);
    $payment_method = $conn->real_escape_string($_POST['payment_method']);
    $transaction_id = $conn->real_escape_string($_POST['transaction_id']);

    $cart = $_SESSION['cart'];
    $product_ids = array_keys($cart);
    $total = 0;

    $ids = implode(',', array_map('intval', $product_ids));
    $result = $conn->query("SELECT * FROM products WHERE id IN ($ids)");

    $order_items = [];
    while ($row = $result->fetch_assoc()) {
        $qty = $cart[$row['id']];
        $subtotal = $row['price'] * $qty;
        $total += $subtotal;
        $order_items[] = ['product_id' => $row['id'], 'quantity' => $qty, 'price' => $row['price']];
    }

    // Insert into orders table
    $conn->query("INSERT INTO orders (user_id, total, order_date, address, payment_method, transaction_id) VALUES (
        $user_id, $total, NOW(), '$address, $city, $state, $zip', '$payment_method', '$transaction_id'
    )");

    $order_id = $conn->insert_id;

    // Insert order items
    foreach ($order_items as $item) {
        $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (
            $order_id, {$item['product_id']}, {$item['quantity']}, {$item['price']}
        )");
    }

    // Clear cart
    $_SESSION['cart'] = [];

    header("Location: user_profile.php?tab=orders");
    exit();
}
?>

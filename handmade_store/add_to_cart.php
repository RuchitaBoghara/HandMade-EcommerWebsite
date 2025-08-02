<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
  $product_id = (int)$_POST['product_id'];

  if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
  }

  if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id] += 1;
  } else {
    $_SESSION['cart'][$product_id] = 1;
  }

  header('Location: cart.php');
  exit();
}

header('Location: store.php');
exit();

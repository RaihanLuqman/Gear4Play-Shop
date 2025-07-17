<?php
session_start();
// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'gear4play_shop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pastikan session user_id ada
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Validasi dan sanitasi order_id dan status
if (!isset($_POST['order_id']) || !is_numeric($_POST['order_id'])) {
    die("Error: Invalid order ID.");
}

if (!isset($_POST['status'])) {
    die("Error: Invalid status.");
}

$order_id = intval($_POST['order_id']);
$status = $conn->real_escape_string($_POST['status']);

// Update status pengiriman di database
$stmt = $conn->prepare("UPDATE pengiriman SET status = ? WHERE order_id = ?");
$stmt->bind_param("si", $status, $order_id);
$stmt->execute();

// Redirect kembali ke halaman live_location.php dengan order_id
header("Location: live_location.php?order_id=$order_id");
exit();
?>

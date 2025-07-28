<?php
session_start();
include('../includes/db_connect.php');

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Pastikan transaction_id ada dalam request
if (!isset($_POST['transaction_id'])) {
    echo "Error: Missing transaction ID.";
    exit;
}

$transaction_id = $_POST['transaction_id'];
$user_id = $_SESSION['user_id'];

// Update status pesanan menjadi "Cancelled"
$query = "UPDATE orderdetail od
          JOIN checkout c ON od.id_checkout = c.id_checkout
          SET od.status = 'Cancelled'
          WHERE c.transaction_id = ? AND od.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $transaction_id, $user_id);

if ($stmt->execute()) {
    // Redirect kembali ke halaman My Order
    header("Location: myorder.php?message=order_cancelled");
    exit;
} else {
    echo "Error: " . $stmt->error;
}
?>

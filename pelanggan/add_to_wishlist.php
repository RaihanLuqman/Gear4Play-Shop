<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'gear4play_shop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pastikan session id_user ada
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}

$user_id = $_SESSION['user_id'];
$id_product = $_POST['id_product'];
$active = isset($_POST['active']) ? (int) $_POST['active'] : 0; // Pastikan active adalah 0 atau 1

// Cek apakah produk ada di tabel product
$product_query = $conn->prepare("SELECT id_product FROM product WHERE id_product = ?");
$product_query->bind_param("i", $id_product);
$product_query->execute();
$product_result = $product_query->get_result();

if ($product_result->num_rows == 0) {
    die("Produk tidak ditemukan di katalog.");
}

// Cek apakah produk sudah ada di wishlist
$query = $conn->prepare("SELECT * FROM wishlist WHERE id_user = ? AND id_product = ?");
$query->bind_param("ii", $user_id, $id_product);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    // Jika produk ada dalam wishlist dan active = false (unlike), hapus produk
    if ($active == 0) {
        $stmt = $conn->prepare("DELETE FROM wishlist WHERE id_user = ? AND id_product = ?");
        $stmt->bind_param("ii", $user_id, $id_product);
        $stmt->execute();
    }
} else {
    // Jika produk tidak ada dalam wishlist dan active = true (like), tambahkan produk ke wishlist
    if ($active == 1) {
        $stmt = $conn->prepare("INSERT INTO wishlist (id_user, id_product) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $id_product);
        $stmt->execute();
    }
}

// Redirect ke halaman sebelumnya setelah memproses
header("Location: pelanggan.php");
exit();
?>

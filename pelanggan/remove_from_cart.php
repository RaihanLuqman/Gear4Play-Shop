<?php
session_start();
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'gear4play_shop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pastikan session id_user ada
if (!isset($_SESSION['user_id'])) {
    // Jika user tidak login, arahkan ke halaman login
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil id_cart_item dari URL
if (isset($_GET['id'])) {
    $cart_item_id = $_GET['id'];

    // Hapus item dari cart
    $remove_query = "DELETE FROM cartitem WHERE id_cart_item = '$cart_item_id' AND id_cart IN (SELECT id_cart FROM cart WHERE id_pelanggan = '$user_id')";
    if ($conn->query($remove_query) === TRUE) {
        // Redirect kembali ke halaman cart setelah item dihapus
        header("Location: cart.php");
        exit();
    } else {
        echo "Error removing item: " . $conn->error;
    }
} else {
    echo "Invalid item ID.";
}

$conn->close();
?>

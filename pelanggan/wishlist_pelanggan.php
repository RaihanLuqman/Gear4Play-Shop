<?php
// Pastikan user sudah login
$id_user = $_SESSION['id_user'];

// Ambil produk dari wishlist
$stmt = $conn->prepare("SELECT p.id_product, p.name, p.image FROM products p INNER JOIN wishlist w ON p.id_product = w.id_product WHERE w.id_user = ?");
$stmt->execute([$id_user]);
$wishlist_items = $stmt->fetchAll();

foreach ($wishlist_items as $item) {
    // Menampilkan produk
    echo '<div class="product">';
    echo '<img src="' . $item['image'] . '" alt="' . $item['name'] . '">';
    echo '<p>' . $item['name'] . '</p>';
    echo '<button class="heart-btn" data-product-id="' . $item['id_product'] . '" style="color: #1EFF00;">&#10084;</button>'; // Warna hijau karena ada di wishlist
    echo '</div>';
}
?>

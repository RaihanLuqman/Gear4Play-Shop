<?php
session_start();

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'gear4play_shop');
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data produk dari keranjang atau pembelian langsung
$checkout_items = [];
if (isset($_POST['from_cart']) && $_POST['from_cart'] == '1') {
    // Ambil item dari keranjang
    $cart_query = $conn->prepare("
        SELECT p.id_product, p.product_name, p.price, c.quantity 
        FROM cart c 
        JOIN product p ON c.product_id = p.id_product 
        WHERE c.user_id = ?
    ");
    $cart_query->bind_param('i', $user_id);
    $cart_query->execute();
    $result = $cart_query->get_result();
    while ($row = $result->fetch_assoc()) {
        $checkout_items[] = $row;
    }
    $cart_query->close();
} else {
    // Pembelian langsung
    if (isset($_POST['id_product'], $_POST['quantity']) && is_numeric($_POST['quantity'])) {
        $product_id = intval($_POST['id_product']);
        $quantity = intval($_POST['quantity']);

        $product_query = $conn->prepare("
            SELECT id_product, product_name, price 
            FROM product 
            WHERE id_product = ?
        ");
        $product_query->bind_param('i', $product_id);
        $product_query->execute();
        $result = $product_query->get_result();
        if ($result->num_rows > 0) {
            $product_data = $result->fetch_assoc();
            $product_data['quantity'] = $quantity;
            $checkout_items[] = $product_data;
        }
        $product_query->close();
    }
}

// Proses checkout
if (!empty($checkout_items)) {
    // Mulai transaksi
    $conn->begin_transaction();
    try {
        // Simpan ke tabel orders
        $total_price = 0;
        foreach ($checkout_items as $item) {
            $total_price += $item['price'] * $item['quantity'];
        }

        $order_query = $conn->prepare("
            INSERT INTO orders (user_id, total_price, status, order_date) 
            VALUES (?, ?, 'Pending', NOW())
        ");
        $order_query->bind_param('id', $user_id, $total_price);
        $order_query->execute();
        $order_id = $conn->insert_id;
        $order_query->close();

        // Simpan detail pesanan dan kurangi stok
        $detail_query = $conn->prepare("
            INSERT INTO order_detail (order_id, product_id, quantity, price) 
            VALUES (?, ?, ?, ?)
        ");
        $update_stock_query = $conn->prepare("
            UPDATE product 
            SET stock = stock - ? 
            WHERE id_product = ?
        ");

        foreach ($checkout_items as $item) {
            $product_id = $item['id_product'];
            $quantity = $item['quantity'];
            $price = $item['price'];

            $detail_query->bind_param('iiid', $order_id, $product_id, $quantity, $price);
            $detail_query->execute();

            $update_stock_query->bind_param('ii', $quantity, $product_id);
            $update_stock_query->execute();
        }
        $detail_query->close();
        $update_stock_query->close();

        // Hapus data dari keranjang jika pembelian lewat keranjang
        if (isset($_POST['from_cart']) && $_POST['from_cart'] == '1') {
            $delete_cart_query = $conn->prepare("
                DELETE FROM cart 
                WHERE user_id = ?
            ");
            $delete_cart_query->bind_param('i', $user_id);
            $delete_cart_query->execute();
            $delete_cart_query->close();
        }

        // Commit transaksi
        $conn->commit();
        echo "Checkout berhasil! Pesanan Anda telah dibuat.";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Gagal melakukan checkout: " . $e->getMessage();
    }
} else {
    echo "Tidak ada item untuk checkout.";
}

$conn->close();
?>

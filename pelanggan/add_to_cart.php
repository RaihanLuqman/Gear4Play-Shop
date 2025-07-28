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
$product_id = intval($_POST['id_product']); // Pastikan ID produk berupa integer
$quantity = intval($_POST['quantity']); // Pastikan kuantitas berupa integer

if ($quantity < 1) {
    $quantity = 1; // Minimal kuantitas adalah 1
}

// Ambil data produk (product_name, price, dan stock)
$product_query = "SELECT product_name, price, stock FROM product WHERE id_product = '$product_id'";
$product_result = $conn->query($product_query);
$product_data = $product_result->fetch_assoc();

if ($product_data) {
    $product_name = $product_data['product_name'];
    $price = $product_data['price'];
    $stock = $product_data['stock'];
    
    // Validasi: Kuantitas tidak boleh melebihi stok
    if ($quantity > $stock) {
        $quantity = $stock;
    }

    $total_price = $price * $quantity;

    // Ambil atau buat cart untuk pengguna
    $cart_query = "SELECT id_cart FROM cart WHERE id_pelanggan = '$user_id'";
    $cart_result = $conn->query($cart_query);
    $cart_data = $cart_result->fetch_assoc();

    if ($cart_data) {
        $cart_id = $cart_data['id_cart'];
    } else {
        // Jika pengguna belum memiliki cart, buat cart baru
        $create_cart_query = "INSERT INTO cart (id_pelanggan, created_date) VALUES ('$user_id', NOW())";
        if ($conn->query($create_cart_query) === TRUE) {
            $cart_id = $conn->insert_id;
        } else {
            die("Error creating cart: " . $conn->error);
        }
    }

    // Cek apakah produk sudah ada di dalam cart
    $cart_item_query = "SELECT id_cart_item, quantity FROM cartitem WHERE id_cart = '$cart_id' AND id_product = '$product_id'";
    $cart_item_result = $conn->query($cart_item_query);

    if ($cart_item_result->num_rows > 0) {
        // Update jumlah dan total price jika produk sudah ada di cart
        $cart_item_data = $cart_item_result->fetch_assoc();
        $cart_item_id = $cart_item_data['id_cart_item'];
        $existing_quantity = $cart_item_data['quantity'];

        // Hitung kuantitas baru (tidak boleh melebihi stok)
        $new_quantity = min($existing_quantity + $quantity, $stock);
        $new_total_price = $new_quantity * $price;

        $update_query = "UPDATE cartitem 
                         SET quantity = $new_quantity, 
                             total_price = $new_total_price 
                         WHERE id_cart_item = '$cart_item_id'";
        $conn->query($update_query);
    } else {
        // Jika produk belum ada di cart, tambahkan produk baru
        $insert_query = "INSERT INTO cartitem (id_cart, id_product, product_name, quantity, price, total_price) 
                         VALUES ('$cart_id', '$product_id', '$product_name', '$quantity', '$price', '$total_price')";
        if (!$conn->query($insert_query)) {
            die("Error inserting item into cart: " . $conn->error);
        }
    }

    // Redirect ke halaman cart setelah menambahkan produk
    header("Location: cart.php");
    exit();
} else {
    // Jika produk tidak ditemukan
    die("Product not found.");
}
?>

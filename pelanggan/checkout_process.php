<?php
session_start();
include('../includes/db_connect.php'); // Include your database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Ensure cart_id and payment_method are available
if (!isset($_POST['cart_id']) || !isset($_POST['payment_method'])) {
    echo "Error: Missing cart_id or payment_method.";
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_id = $_POST['cart_id'];
$payment_method = $_POST['payment_method'];

// Ensure total_price is received
if (isset($_POST['total_price'])) {
    $total_price = $_POST['total_price'];
} else {
    echo "Error: Total price is missing!";
    exit;
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Insert a summary entry into orders table (only one row for the order)
    $status = 'pending';
    $payment_status = 'unpaid'; // Default payment status
    $order_date = date('Y-m-d H:i:s');
    $transaction_id = uniqid('trx_');

    // Insert order summary into orders table
    $query = "INSERT INTO orders (user_id, total_price, order_date, status, transaction_id, payment_method) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'idssss', $user_id, $total_price, $order_date, $status, $transaction_id, $payment_method);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error inserting into orders: ' . mysqli_error($conn));
    }
    $order_id = mysqli_insert_id($conn); // Get the last inserted order_id

    // Loop through product sent via POST and insert each one
    $product_names = [];
    $total_quantity = 0;
    $product_total_prices = 0;

    if (isset($_POST['product'])) {
        foreach ($_POST['product'] as $product_data) {
            $product_id = $product_data['id'];
            $product_name = $product_data['name'];
            $quantity = $product_data['quantity'];
            $price = $product_data['price'];
            $total_price = $product_data['total_price'];

            // Ambil harga produk dari database
            $query = "SELECT price FROM product WHERE id_product = ?";
            $stmt_product = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt_product, 'i', $product_id);
            mysqli_stmt_execute($stmt_product);
            $result_product = mysqli_stmt_get_result($stmt_product);

            if ($result_product && $row = mysqli_fetch_assoc($result_product)) {
                $product_price = $row['price'];
                $total_item_price = $product_price * $quantity;

                // Simpan data produk untuk update kolom product_name, quantity, dan total_price
                $product_names[] = $quantity . " " . $product_name;
                $total_quantity += $quantity;
                $product_total_prices += $total_item_price;
            } else {
                throw new Exception('Product not found for ID ' . $product_id);
            }
        }
    } else {
        throw new Exception('No products found.');
    }

    // Update order with all products in the same order entry
    $product_names_str = implode(", ", $product_names);

    // Update the orders table with product names, total quantity, and total prices
    $query = "UPDATE orders 
              SET product_name = ?, quantity = ?, total_price = ? 
              WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sidi', $product_names_str, $total_quantity, $product_total_prices, $order_id);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error updating order items: ' . mysqli_error($conn));
    }

    // Insert a summary entry into orderdetail table
    $batas_waktu_pembayaran = date('Y-m-d H:i:s', strtotime('+2 days'));

    $query = "INSERT INTO orderdetail (user_id, order_id, status, payment_status, order_date, total_price) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'iiisss', $user_id, $order_id, $status, $payment_status, $order_date, $total_price);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error inserting into orderdetail: ' . mysqli_error($conn));
    }

    // Delete items from cart after checkout is complete
    $query = "DELETE FROM cartitem WHERE id_cart = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $cart_id);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error clearing cart: ' . mysqli_error($conn));
    }

    // Commit transaction
    mysqli_commit($conn);

    // Redirect to My Order page
    header("Location: myorder.php?order_id=$order_id");
    exit;
} catch (Exception $e) {
    // Rollback if an error occurs
    mysqli_rollback($conn);
    echo "Error: " . $e->getMessage();
}

// Function to get items from the cart
function getCartItems($cart_id)
{
    global $conn;
    $query = "SELECT c.id_product, p.product_name, p.price, c.quantity
              FROM cartitem c
              JOIN product p ON c.id_product = p.id_product
              WHERE c.id_cart = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $cart_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

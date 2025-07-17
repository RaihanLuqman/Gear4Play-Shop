<?php
session_start();

// Connect to the database
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

// Ambil data user dari database
$user_query = "SELECT username, profile_picture FROM user WHERE id_user = '$user_id'";
$user_result = $conn->query($user_query);
$user_data = $user_result->fetch_assoc();

$username = $user_data['username'];
$profile_picture = '../assets/images/default-profile.png'; // Default

if (!empty($_SESSION['user_image']) && file_exists('uploads/' . $_SESSION['user_image'])) {
    $profile_picture = 'uploads/' . $_SESSION['user_image'];
}

// Ambil data produk yang ada di cart
$cart_items = [];

// Jika datang dari keranjang
if (isset($_GET['cart_id']) && isset($_GET['cart_items'])) {
    $cart_id = $_GET['cart_id'];
    $cart_items_ids = $_GET['cart_items'];

    if (!empty($cart_items_ids)) {
        $ids_placeholder = implode(',', array_fill(0, count($cart_items_ids), '?'));
        $stmt = $conn->prepare("
            SELECT p.id_product, p.product_name AS product_name, p.price, p.image_url, c.quantity 
            FROM cartitem c 
            JOIN product p ON c.id_product = p.id_product 
            WHERE c.id_cart = ? AND c.id_cart_item IN ($ids_placeholder)
        ");

        $stmt->bind_param('i' . str_repeat('i', count($cart_items_ids)), $cart_id, ...$cart_items_ids);
        $stmt->execute();
        $result = $stmt->get_result();
        $cart_items = $result->fetch_all(MYSQLI_ASSOC);
    }
}

// Jika datang langsung dari halaman produk (misalnya tombol "Beli")
if (isset($_GET['id_product']) && isset($_GET['quantity'])) {
    $id_product = $_GET['id_product'];
    $quantity = $_GET['quantity'];

    // Ambil detail produk dari database
    $product_query = "SELECT id_product, product_name, price, image_url FROM product WHERE id_product = '$id_product'";
    $product_result = $conn->query($product_query);

    if ($product_result->num_rows > 0) {
        $product = $product_result->fetch_assoc();
        $product['quantity'] = $quantity;
        $cart_items[] = $product;  // Menambahkan produk yang dibeli langsung ke array cart_items
    }
}

// Total harga produk di cart
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="/Gear4Play_Shop/assets/checkout.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>


</head>

<style>
    .back-btn {
        display: inline-block;
        background-color: #444;
        color: #fff;
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: 0.2s ease;
        margin-bottom: 5px;
    }

    .back-btn:hover {
        background-color: #555;
    }
</style>

<body>
    <div class="checkout-wrapper">
        <div class="checkout-header">
            <h1 style="color:white">CHECKOUT</h1>
        </div>
        <div class="checkout-content">
            <div class="payment-section">
                <a href="cart.php" class="back-btn">← Back to Cart</a>
                <h2>G4P WALLET</h2>
                <label class="payment-option">
                    <input type="radio" name="payment-method" value="G4P_wallet">
                    <span class="icon" style="color:white;"><i class='bx bx-wallet'></i></span>
                    <span style="color:white">G4P Wallet Rp.0.00</span>
                </label>
                <h2>OTHER PAYMENT METHODS</h2>
                <label class="payment-option">
                    <input type="radio" name="payment-method" value="qris">
                    <span class="icon" style="color:white"><i class='bx bx-qr-scan'></i></span>
                    <span style="color:white">Qris</span>
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment-method" value="paypal">
                    <span class="icon" style="color:white"><i class='bx bxl-paypal'></i></span>
                    <span style="color:white">PayPal</span>
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment-method" value="dana">
                    <span class="icon" style="color:white"><img src="../admin/uploaded_image/logo_dana.png" alt="Dana"></span>
                    <span style="color:white">DANA</span>
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment-method" value="ovo">
                    <span class="icon" style="color:white"><img src="../admin/uploaded_image/logo_ovo.png" alt="OVO"></span>
                    <span style="color:white">OVO</span>
                </label>
            </div>
            <div class="summary-section">
                <h2>ORDER SUMMARY</h2>
                <?php foreach ($cart_items as $item): ?>
                    <?php
                    // Pastikan path gambar sesuai dengan lokasi di folder admin
                    $imagePath = '../admin/' . $item['image_url'];
                    ?> <div class="summary-item" style="color:white">
                        <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                        <div>
                            <p><strong><?php echo $item['product_name']; ?></strong></p>
                            <p>Quantity: <?php echo $item['quantity']; ?></p>
                            <p>Price: Rp.<?php echo number_format($item['price'], 0, ',', '.'); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="summary-total">
                    <p style="color:white">Total Price: Rp.<?php echo number_format($total_price, 0, ',', '.'); ?></p>
                </div>
                <form action="checkout_process.php" method="POST">
                    <input type="hidden" name="cart_id" value="<?= $cart_id ?>">
                    <input type="hidden" name="total_price" value="<?= $total_price ?>">
                    <input type="hidden" name="payment_method" value="G4P_wallet"> <!-- Tambahkan sesuai pilihan pembayaran -->
                    <?php foreach ($cart_items as $item): ?>
                        <input type="hidden" name="product[<?= $item['id_product'] ?>][id]" value="<?= $item['id_product'] ?>">
                        <input type="hidden" name="product[<?= $item['id_product'] ?>][name]" value="<?= $item['product_name'] ?>">
                        <input type="hidden" name="product[<?= $item['id_product'] ?>][quantity]" value="<?= $item['quantity'] ?>">
                        <input type="hidden" name="product[<?= $item['id_product'] ?>][price]" value="<?= $item['price'] ?>">
                        <input type="hidden" name="product[<?= $item['id_product'] ?>][total_price]" value="<?= $item['price'] * $item['quantity'] ?>">
                    <?php endforeach; ?>

                    <button type="submit" class="place-order-btn" onclick="setPaymentMethod()">PLACE ORDER</button>
                </form>

            </div>
        </div>
    </div>

    <script>
        // Function to set the selected payment method in the hidden input
        function setPaymentMethod(event) {
            const paymentMethods = document.querySelectorAll('input[name="payment-method"]:checked');
            if (paymentMethods.length > 0) {
                document.getElementById('payment-method-input').value = paymentMethods[0].value;
            } else {
                alert('Please select a payment method');
                event.preventDefault(); // Prevent form submission if no payment method is selected
            }
        }

        // Attach the setPaymentMethod function to the form submit event
        const form = document.querySelector('form');
        form.addEventListener('submit', function(event) {
            setPaymentMethod(event);
        });
    </script>

</body>

</html>
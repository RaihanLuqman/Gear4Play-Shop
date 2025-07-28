<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'gear4play_shop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil id_cart
$cart_query = "SELECT id_cart FROM cart WHERE id_pelanggan = '$user_id'";
$cart_result = $conn->query($cart_query);
$cart_data = $cart_result->fetch_assoc();

if ($cart_data) {
    $cart_id = $cart_data['id_cart'];
} else {
    $create_cart_query = "INSERT INTO cart (id_pelanggan, created_date) VALUES ('$user_id', NOW())";
    if ($conn->query($create_cart_query) === TRUE) {
        $cart_id = $conn->insert_id;
    } else {
        die("Error creating cart: " . $conn->error);
    }
}

// Ambil item dalam cart + stok produk
$cart_items_query = "
    SELECT ci.id_cart_item, p.product_name, ci.quantity, ci.price, ci.total_price, p.image_url, p.stock
    FROM cartitem ci
    JOIN product p ON ci.id_product = p.id_product
    WHERE ci.id_cart = '$cart_id'
";

$cart_items_result = $conn->query($cart_items_query);

$total_price = 0;
$cart_items = [];
$warning_items = [];

while ($row = $cart_items_result->fetch_assoc()) {
    if ($row['quantity'] > $row['stock']) {
        $row['quantity'] = $row['stock'];
        $row['total_price'] = $row['stock'] * $row['price'];
        $warning_items[] = $row['product_name'];
    }
    $cart_items[] = $row;
    $total_price += $row['total_price'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Gear4Play</title>
    <btn rel="stylesheet" href="path/to/your/styles.css">
        <style>
            body {
                font-family: 'Poppins', sans-serif;
                background-color: #121212;
                color: #fff;
                margin: 0;
                padding: 0;
            }

            .cart-container {
                width: 85%;
                margin: 50px auto;
                background-color: #1e1e1e;
                border-radius: 12px;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
                padding: 40px;
            }

            h1 {
                font-size: 32px;
                margin-bottom: 20px;
                color: #2ecc71;
            }

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
                margin-bottom: 25px;
            }

            .back-btn:hover {
                background-color: #555;
            }


            .cart-items {
                display: flex;
                flex-direction: column;
                gap: 25px;
            }

            .cart-item {
                display: flex;
                background-color: #2a2a2a;
                border-radius: 12px;
                overflow: hidden;
                transition: transform 0.2s;
            }

            .cart-item:hover {
                transform: scale(1.01);
            }

            .cart-item img {
                width: 100px;
                height: 100px;
                object-fit: cover;
                border-right: 1px solid #444;
            }

            .cart-item-info {
                flex: 1;
                padding: 15px 20px;
            }

            .cart-item-name {
                font-size: 20px;
                font-weight: 600;
                color: #ffffff;
            }

            .cart-item-quantity,
            .cart-item-price,
            .cart-item-total {
                font-size: 15px;
                color: #ccc;
                margin-top: 6px;
            }

            .cart-item-actions {
                display: flex;
                align-items: center;
                padding: 0 20px;
                background-color: #2a2a2a;
            }

            .cart-item-actions a {
                color: #e74c3c;
                text-decoration: none;
                font-size: 15px;
                font-weight: 500;
            }

            .cart-item-actions a:hover {
                text-decoration: underline;
            }

            .cart-summary {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #444;
                font-size: 18px;
            }

            .cart-summary .total-price {
                font-size: 24px;
                font-weight: bold;
                color: #2ecc71;
            }

            .checkout-btn {
                display: block;
                width: 100%;
                margin-top: 30px;
                padding: 15px 0;
                background: linear-gradient(90deg, #27ae60, #2ecc71);
                border: none;
                color: #fff;
                font-size: 18px;
                border-radius: 8px;
                cursor: pointer;
                transition: 0.3s;
                font-weight: 600;
            }

            .checkout-btn:hover {
                filter: brightness(1.1);
            }

            .warning-box {
                background-color: #ffc107;
                color: #1e1e1e;
                border-radius: 8px;
                padding: 15px 20px;
                margin-bottom: 20px;
                font-size: 15px;
            }

            .warning-box ul {
                margin-top: 10px;
                padding-left: 20px;
            }

            .remove-btn {
                background-color: #e74c3c;
                color: white;
                border: none;
                padding: 8px 16px;
                border-radius: 6px;
                font-size: 14px;
                cursor: pointer;
                transition: 0.2s ease;
            }

            .remove-btn:hover {
                background-color: #c0392b;
            }
        </style>
</head>

<body>
    <div class="cart-container">
        <h1 style="text-align:center;">Your Shopping Cart</h1>
        <a href="pelanggan.php" class="back-btn">← Back to Store</a>

        <?php if (!empty($warning_items)): ?>
            <div class="warning-box">
                ⚠️ Quantity untuk produk berikut melebihi stok dan telah disesuaikan:
                <ul>
                    <?php foreach ($warning_items as $item): ?>
                        <li><?= htmlspecialchars($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (count($cart_items) > 0): ?>
            <div class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <?php $imagePath = '../admin/' . htmlspecialchars($item['image_url']); ?>
                            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($item['product_name']) ?>">
                            <div>
                                <p class="cart-item-name"><?= htmlspecialchars($item['product_name']) ?></p>
                                <p class="cart-item-quantity">Quantity: <?= htmlspecialchars($item['quantity']) ?> (Stock: <?= $item['stock'] ?>)</p>
                                <p class="cart-item-price">Price: Rp.<?= number_format($item['price'], 2) ?></p>
                                <p class="cart-item-total">Total: Rp.<?= number_format($item['total_price'], 2) ?></p>
                            </div>
                        </div>
                        <div class="cart-item-actions">
                            <form action="remove_from_cart.php" method="GET">
                                <input type="hidden" name="id" value="<?= $item['id_cart_item'] ?>">
                                <button type="submit" class="remove-btn">Remove</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <span>Total Price:</span>
                <span class="total-price">Rp.<?= number_format($total_price, 2) ?></span>
            </div>

            <?php if (empty($warning_items)): ?>
                <form action="checkout.php" method="GET">
                    <input type="hidden" name="cart_id" value="<?= $cart_id ?>">
                    <input type="hidden" name="total_price" value="<?= $total_price ?>">
                    <?php foreach ($cart_items as $item): ?>
                        <input type="hidden" name="cart_items[]" value="<?= $item['id_cart_item'] ?>">
                    <?php endforeach; ?>
                    <button type="submit" class="checkout-btn">Proceed to Checkout</button>
                </form>
            <?php else: ?>
                <p style="color:red; margin-top: 20px;">Perbaiki jumlah produk yang melebihi stok sebelum melanjutkan checkout.</p>
            <?php endif; ?>

        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>
</body>

</html>
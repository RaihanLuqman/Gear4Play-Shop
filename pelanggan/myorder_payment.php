<?php
session_start();

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'gear4play_shop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pastikan session id_user ada
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$transaction_id = $_GET['transaction_id'] ?? null;

// Ambil detail order berdasarkan transaction_id
$order_query = "SELECT * FROM orders WHERE transaction_id = ? AND user_id = ?";
$stmt = $conn->prepare($order_query);
$stmt->bind_param("si", $transaction_id, $user_id);
$stmt->execute();
$order_result = $stmt->get_result();
$order = $order_result->fetch_assoc();

// Redirect jika order tidak ditemukan
if (!$order) {
    header("Location: myorder.php");
    exit();
}

// Hitung batas waktu pembayaran (24 jam dari waktu order)
$order_time = strtotime($order['order_date']);
$payment_deadline = $order_time + 60;
$current_time = time();

// Periksa apakah pembayaran sudah melewati batas waktu
if ($current_time > $payment_deadline && $order['payment_status'] != 'Paid') {
    $update_status_query = "UPDATE orders SET payment_status = 'Cancelled' WHERE transaction_id = ?";
    $update_stmt = $conn->prepare($update_status_query);
    $update_stmt->bind_param("s", $transaction_id);
    $update_stmt->execute();
    $order['payment_status'] = 'Cancelled';
}

// Proses pembayaran jika tombol "Pay" ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
    // Update status pembayaran menjadi 'Paid'
    $update_status_query = "UPDATE orders SET payment_status = 'Paid' WHERE transaction_id = ?";
    $update_stmt = $conn->prepare($update_status_query);
    $update_stmt->bind_param("s", $transaction_id);
    $update_stmt->execute();
    $order['payment_status'] = 'Paid';

    // Query untuk menambah entri pengiriman setelah pembayaran
    $insert_pengiriman_query = "INSERT INTO pengiriman (order_id, id_kurir, status, waktuAwal_kirim, live_location_item, estimasi_sampai, no_resi) 
                             VALUES (?, ?, 'pending', NOW(), 'Menunggu pickup kurir', DATE_ADD(NOW(), INTERVAL 3 DAY), CONCAT('resi-', ?, '-', UNIX_TIMESTAMP()))";

    $stmt_pengiriman = $conn->prepare($insert_pengiriman_query);

    // Asumsikan Anda sudah memiliki id_kurir (misalnya ID kurir yang dipilih oleh pengguna atau sistem)
    $id_kurir = 5; // Contoh ID kurir, sesuaikan dengan kebutuhan

    $stmt_pengiriman->bind_param("iii", $order['order_id'], $id_kurir, $order['order_id']);
    $stmt_pengiriman->execute();

    // Cek apakah insert pengiriman berhasil
    if ($stmt_pengiriman->affected_rows > 0) {
        echo "Pengiriman berhasil ditambahkan.";
    } else {
        echo "Terjadi kesalahan saat menambahkan data pengiriman.";
    }
    $stmt_pengiriman->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gear4Play - Order Payment</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="/Gear4Play_Shop/assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #222;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            /* Menyusun elemen secara horizontal di tengah */
            align-items: center;
            /* Menyusun elemen secara vertikal di tengah */
            height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 900px;
            padding: 30px;
            background-color: #333;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            /* Membuat kontainer bersifat kolom */
            align-items: center;
            /* Memastikan elemen dalam kontainer berada di tengah */
        }

        /* Header Section */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            /* Agar header memanfaatkan lebar penuh */
            margin-bottom: 20px;
        }

        .header a {
            font-size: 18px;
            text-decoration: none;
            color: #bbb;
            display: flex;
            align-items: center;
            transition: color 0.3s;
        }

        .header a:hover {
            color: #fff;
        }

        /* Order Detail Section */
        .order-detail {
            background-color: #444;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            width: 100%;
            /* Memastikan elemen order detail menggunakan lebar penuh */
            max-width: 600px;
            /* Membatasi lebar maksimum */
        }

        .order-detail h2 {
            font-size: 26px;
            margin-bottom: 15px;
            color: #f5a623;
        }

        .order-detail p {
            font-size: 18px;
            margin: 10px 0;
        }

        .status-message {
            font-weight: bold;
            color: #e74c3c;
        }

        .pay-button {
            background: linear-gradient(145deg, #34c759, #28a745);
            color: #fff;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            font-size: 18px;
            cursor: pointer;
            width: 100%;
            transition: transform 0.3s, background 0.3s;
        }

        .pay-button:hover {
            transform: scale(1.1);
            background: linear-gradient(145deg, #28a745, #34c759);
        }

        .countdown {
            font-size: 24px;
            font-weight: bold;
            color: #f39c12;
            margin-top: 20px;
        }

        .expired {
            color: #e74c3c;
        }

        .completed {
            color: #2ecc71;
        }

        /* Countdown timer animation */
        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }

        .blinking {
            animation: blink 1s infinite;
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px;
            }

            .pay-button {
                padding: 12px 20px;
                font-size: 16px;
            }

            .order-detail h2 {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <a href="myorder.php" style="margin-top:50px">
                <i class="bx bx-left-arrow-alt" style="margin-right:10px"></i> Back
            </a>
        </div>

        <div class="order-detail">
            <h2>Order Details</h2>
            <p><strong>Transaction ID:</strong> <?= htmlspecialchars($order['transaction_id']) ?></p>
            <p><strong>Total Price:</strong> Rp.<?= number_format($order['total_price'], 0, ',', '.') ?></p>
            <p><strong>Payment Status:</strong> <?= htmlspecialchars($order['payment_status']) ?></p>

            <?php if ($order['payment_status'] == 'Cancelled'): ?>
                <p class="status-message">Payment time has expired.</p>
            <?php elseif ($order['payment_status'] == 'Paid'): ?>
                <p class="completed">Payment completed.</p>
            <?php else: ?>
                <p><strong>Payment Deadline:</strong> <span class="countdown" id="countdown"></span></p>
                <form method="POST">
                    <button type="submit" name="pay" class="pay-button">Pay Now</button>
                </form>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const countdownElement = document.getElementById("countdown");
                        let remainingTime = <?= max(0, $payment_deadline - $current_time) ?>;

                        function updateCountdown() {
                            if (remainingTime > 0) {
                                const minutes = Math.floor(remainingTime / 60);
                                const seconds = remainingTime % 60;
                                countdownElement.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                                remainingTime--;
                            } else {
                                countdownElement.textContent = "Time is up!";
                                countdownElement.classList.add("expired");
                                clearInterval(countdownInterval);
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            }
                        }

                        const countdownInterval = setInterval(updateCountdown, 1000);
                        updateCountdown();
                    });
                </script>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>

kurir
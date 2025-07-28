<?php
session_start();
// Connect to database
$conn = new mysqli('localhost', 'root', '', 'gear4play_shop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pastikan session id_user ada
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    // Tangani error jika session user_id tidak ada
    die("You must be logged in to access this page.");
}

// Ambil data user dari database
$user_query = "SELECT username, profile_picture FROM user WHERE id_user = ?";
$stmt_user = $conn->prepare($user_query);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user_data = $user_result->fetch_assoc();

$username = $user_data['username'];
$profile_picture = '../assets/images/default-profile.png'; // Default

if (!empty($_SESSION['user_image']) && file_exists('uploads/' . $_SESSION['user_image'])) {
    $profile_picture = 'uploads/' . $_SESSION['user_image'];
}

$current_page = basename($_SERVER['PHP_SELF']);

// Cek apakah pembayaran berhasil dan lakukan pembaruan status serta pengiriman
if (isset($_GET['transaction_id']) && $_GET['status'] == 'paid') {
    $transaction_id = $_GET['transaction_id'];

    // Ambil order_id berdasarkan transaction_id
    $sql_order = "SELECT order_id FROM orders WHERE transaction_id = ? AND user_id = ? AND payment_status != 'paid'";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("si", $transaction_id, $user_id);
    $stmt_order->execute();
    $order_result = $stmt_order->get_result();

    if ($order_result->num_rows > 0) {
        $order = $order_result->fetch_assoc();
        $order_id = $order['order_id'];

        // Update status pembayaran pesanan menjadi 'paid'
        $sql_update = "UPDATE orders SET payment_status = 'paid' WHERE order_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("i", $order_id);
        $stmt_update->execute();

        // Jika update berhasil, tambahkan data pengiriman
        if ($stmt_update->affected_rows > 0) {
            // Buat nomor resi otomatis berdasarkan order_id
            $no_resi = 'RESI' . str_pad($order_id, 5, '0', STR_PAD_LEFT);

            // Tentukan data pengiriman
            $status = 'pending';  // Status pengiriman bisa berubah sesuai proses
            $waktuAwal_kirim = date('Y-m-d H:i:s');  // Waktu kirim saat ini
            $live_location_item = "Lokasi Pengiriman: Alamat Pengiriman, Kota, Negara"; // Sesuaikan dengan data pengiriman
            $estimasi_sampai = date('Y-m-d H:i:s', strtotime('+3 days'));  // Estimasi sampai 3 hari dari sekarang
            $id_kurir = 5;  // Ganti dengan ID kurir yang sesuai di database Anda

            // Masukkan data pengiriman ke tabel pengiriman
            $sql_pengiriman = "INSERT INTO pengiriman (order_id, id_kurir, status, waktuAwal_kirim, live_location_item, estimasi_sampai, no_resi) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_pengiriman = $conn->prepare($sql_pengiriman);
            $stmt_pengiriman->bind_param("iissssss", $order_id, $id_kurir, $status, $waktuAwal_kirim, $live_location_item, $estimasi_sampai, $no_resi);
            $stmt_pengiriman->execute();

            // Cek apakah pengiriman berhasil dimasukkan
            if ($stmt_pengiriman->affected_rows > 0) {
                echo "Pengiriman berhasil dimasukkan.";
            } else {
                echo "Gagal memasukkan data pengiriman.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gear4Play</title>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Gear4Play_Shop/assets/style.css">
    <link rel="stylesheet" href="/Gear4Play_Shop/assets/sidebar.css">
    <link rel="stylesheet" href="/Gear4Play_Shop/assets/myorder.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="../includes/script_profile.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <a href="/Gear4Play_Shop/<?= htmlspecialchars($home_page) ?>"><img src="/Gear4Play_Shop/assets/images/Logo.png" alt="Gear4Play_Shop Logo" class="logo"></a>
            <i class="bx bx-menu" id="btn_menu"></i>
            <nav class="content-sidebar">
                <ul>
                    <li class="<?= ($current_page == 'pelanggan.php') ? 'active' : ''; ?>">
                        <a href="pelanggan.php">
                            <i class="bx bxs-cart"></i>
                            <span class="nav-item">Store</span>
                        </a>
                        <span class="tooltip">Store</span>
                    </li>
                    <li class="<?= ($current_page == 'wishlist.php') ? 'active' : ''; ?>">
                        <a href="wishlist.php">
                            <i class="bx bxs-heart"></i>
                            <span class="nav-item">Favorite</span>
                        </a>
                        <span class="tooltip">Favorite</span>
                    </li>
                    <li class="<?= ($current_page == 'myorder.php') ? 'active' : ''; ?>">
                        <a href="myorder.php">
                            <i class="bx bxs-package"></i>
                            <span class="nav-item">My Order</span>
                        </a>
                        <span class="tooltip">My Order</span>
                    </li>
                    <li>
                        <button onclick=toggleSubMenu(this) class="dropdown-btn">
                            <span class="nav-item">Category</span>
                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed">
                                <path d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z" />
                            </svg>
                        </button>
                        <ul class="sub-menu">
                            <div>
                                <li><a href="#">Keyboard</a></li>
                                <li><a href="#">Mouse</a></li>
                                <li><a href="#">Headset</a></li>
                                <li><a href="#">Monitor</a></li>
                            </div>
                        </ul>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <?php if (isset($_GET['message']) && $_GET['message'] == 'payment_success'): ?>
                <div class="alert alert-success">
                    Pembayaran berhasil dilakukan.
                </div>
            <?php endif; ?>

            <section class="My Order">
                <h2 style="color:white">My Orders</h2>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Order Date</th>
                            <th>Transaction ID</th>
                            <th>Payment Method</th>
                            <th>Items</th>
                            <th>Total Price</th>
                            <th>Payment status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query untuk mengambil data dari tabel orders
                        $sql = "SELECT o.order_date, o.transaction_id, o.payment_method, o.product_name, o.total_price, o.payment_status, o.order_id 
        FROM orders o
        WHERE o.user_id = ? 
        ORDER BY o.order_date DESC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                    <td>" . htmlspecialchars($row['order_date']) . "</td>
                    <td>" . htmlspecialchars($row['transaction_id']) . "</td>
                    <td>" . htmlspecialchars($row['payment_method']) . "</td>
                    <td>" . htmlspecialchars($row['product_name']) . "</td>
                    <td>Rp." . number_format($row['total_price'], 0, ',', '.') . "</td>
                    <td>" . htmlspecialchars($row['payment_status']) . "</td>
                    <td>";
                                if ($row['payment_status'] === 'paid') {
                                    // Jika payment_status = paid, arahkan ke myorder_detail.php
                                    echo "<a href='myorder_detail.php?order_id=" . urlencode($row['order_id']) . "'>
                              <i class='bx bx-chevron-right-circle' style='color:#34c759; font-size:40px;'></i>
                          </a>";
                                } else {
                                    // Jika payment_status = unpaid atau lainnya, arahkan ke myorder_payment.php
                                    echo "<a href='myorder_payment.php?transaction_id=" . urlencode($row['transaction_id']) . "'>
                                  <i class='bx bx-chevron-right-circle' style='color:#ff9500; font-size:40px;'></i>
                              </a>";
                                }
                                echo "</td>
                  </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No orders found</td></tr>";
                        }
                        ?>
                    </tbody>

                </table>
            </section>
        </main>
    </div>
</body>


<script>
    // Menunggu DOM sepenuhnya dimuat sebelum menjalankan script
    document.addEventListener("DOMContentLoaded", function() {
        const btn_menu = document.querySelector('#btn_menu');
        const sidebar = document.querySelector('.sidebar');

        // Fungsi untuk toggle status sidebar (terbuka/tertutup)
        function toggleSidebar() {
            sidebar.classList.toggle('active'); // Toggle status sidebar (terbuka/tertutup)
            btn_menu.classList.toggle('rotate');
            closeAllSubMenus(); // Tutup semua submenu ketika sidebar berubah
        }

        // Menambahkan toggleSubMenu untuk menangani klik pada Category
        function toggleSubMenu(button) {
            if (!sidebar.classList.contains('active')) { // Hanya aktif jika sidebar terbuka
                return; // Tidak lakukan apapun jika sidebar tertutup
            }

            // Cek apakah submenu sudah terbuka atau belum, kemudian toggle
            if (!button.nextElementSibling.classList.contains('show')) {
                closeAllSubMenus(); // Tutup semua submenu sebelum membuka yang baru
            }

            button.nextElementSibling.classList.toggle('show');
            button.classList.toggle('rotate');
        }

        // Fungsi untuk menutup semua submenu
        function closeAllSubMenus() {
            const subMenus = sidebar.querySelectorAll('.sub-menu');
            subMenus.forEach(menu => {
                menu.classList.remove('show');
                const button = menu.previousElementSibling;
                if (button) {
                    button.classList.remove('rotate');
                }
            });
        }

        // Mengatur aksi klik untuk toggle sidebar
        btn_menu.onclick = function() {
            toggleSidebar(); // Panggil fungsi untuk menutup atau membuka sidebar
        };

        // Event listener untuk kategori button (Category)
        const categoryButtons = document.querySelectorAll('.dropdown-btn');
        categoryButtons.forEach(button => {
            button.onclick = function() {
                toggleSubMenu(button); // Toggle submenu ketika kategori diklik
            };
        });
    });
</script>

</html>
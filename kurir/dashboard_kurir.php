<?php
session_start();

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'gear4play_shop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pastikan session id_user ada
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
}

$user_id = $_SESSION['user_id'];

$current_page = basename($_SERVER['PHP_SELF']);

// Ambil data user dari database
$user_query = "SELECT username, profile_picture, role, id_user FROM user WHERE id_user = ?";
$stmt_user = $conn->prepare($user_query);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user_data = $user_result->fetch_assoc();

// Periksa apakah user memiliki role 'kurir'
if (!$user_data || $user_data['role'] !== 'kurir') {
    die("Access denied. This page is for couriers only.");
}

$username = $user_data['username'];
$profile_picture = !empty($_SESSION['user_image']) && file_exists('uploads/' . $_SESSION['user_image'])
    ? 'uploads/' . $_SESSION['user_image']
    : '../assets/images/default-profile.png';

// Gunakan ID kurir dari user yang sedang login
$id_kurir = $user_data['id_user'];

// Tangani pembayaran berhasil dan tambahkan data pengiriman
if (isset($_GET['transaction_id'], $_GET['status']) && $_GET['status'] === 'paid') {
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

        // Update status pembayaran
        $sql_update = "UPDATE orders SET payment_status = 'paid' WHERE order_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("i", $order_id);
        $stmt_update->execute();

        // Tambahkan data pengiriman jika pembayaran berhasil diperbarui
        if ($stmt_update->affected_rows > 0) {
            $no_resi = 'RESI' . str_pad($order_id, 5, '0', STR_PAD_LEFT);
            $status = 'pending';
            $waktuAwal_kirim = date('Y-m-d H:i:s');
            $live_location_item = "Lokasi Pengiriman: Alamat Pengiriman, Kota, Negara";
            $estimasi_sampai = date('Y-m-d H:i:s', strtotime('+3 days'));

            // Masukkan data ke tabel pengiriman
            $sql_pengiriman = "INSERT INTO pengiriman (order_id, id_kurir, status, waktuAwal_kirim, live_location_item, estimasi_sampai, no_resi) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_pengiriman = $conn->prepare($sql_pengiriman);
            $stmt_pengiriman->bind_param("iisssss", $order_id, $id_kurir, $status, $waktuAwal_kirim, $live_location_item, $estimasi_sampai, $no_resi);
            $stmt_pengiriman->execute();

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
                    <li class="<?= ($current_page == 'dashboard_kurir.php') ? 'active' : ''; ?>">
                        <a href="dashboard_kurir.php">
                            <i class='bx bxs-paper-plane'></i>
                            <span class="nav-item">Pengiriman</span>
                        </a>
                        <span class="tooltip">Pengiriman</span>
                    </li>
                    <li class="<?= ($current_page == '../logout.php') ? 'active' : ''; ?>">
                        <a href="../logout.php">
                            <i class='bx bxs-exit'></i>
                            <span class="nav-item">Logout</span>
                        </a>
                        <span class="tooltip">Logout</span>
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
    <h2>Pengiriman</h2>
    <table class="order-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User Name</th>
                <th>Order Date</th>
                <th>Status</th>
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
            // Query untuk mengambil data dari tabel orders dan pengiriman
            $sql = "
                SELECT o.order_id, u.username, o.order_date, p.status, o.transaction_id, 
                       o.payment_method, o.product_name, o.total_price, o.payment_status
                FROM orders o
                JOIN user u ON o.user_id = u.id_user
                JOIN pengiriman p ON o.order_id = p.order_id
                WHERE p.id_kurir = ?
                ORDER BY o.order_date DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_kurir);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . htmlspecialchars($row['order_id']) . "</td>
                        <td>" . htmlspecialchars($row['username']) . "</td>
                        <td>" . htmlspecialchars($row['order_date']) . "</td>
                        <td>" . htmlspecialchars($row['status']) . "</td>
                        <td>" . htmlspecialchars($row['transaction_id']) . "</td>
                        <td>" . htmlspecialchars($row['payment_method']) . "</td>
                        <td>" . htmlspecialchars($row['product_name']) . "</td>
                        <td>Rp." . number_format($row['total_price'], 0, ',', '.') . "</td>
                        <td>" . htmlspecialchars($row['payment_status']) . "</td>
                        <td>";

                    // Arahkan ke halaman detail pengiriman
                    echo "<a href='live_location.php?order_id=" . urlencode($row['order_id']) . "'>
                        <i class='bx bx-chevron-right-circle' style='color:#34c759; font-size:40px;'></i>
                    </a>";

                    echo "</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No deliveries found</td></tr>";
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
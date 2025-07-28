<?php
session_start();
// Koneksi ke database
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

// Validasi dan sanitasi order_id
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die("Error: Invalid order ID.");
}

$order_id = intval($_GET['order_id']);

// Query untuk mengambil data pengiriman
$stmt = $conn->prepare("
    SELECT p.*, b.proof_image, b.proof_text 
    FROM pengiriman p
    LEFT JOIN buktipengiriman b ON p.idPengiriman = b.idPengiriman
    WHERE p.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();

$pengiriman_result = $stmt->get_result();

if ($pengiriman_result->num_rows > 0) {
    $pengiriman_data = $pengiriman_result->fetch_assoc();
} else {
    die("Error: No data found for the provided order ID.");
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gear4Play</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/sidebar.css">
    <script src="../script/script.js" defer></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* KURIR LIVE LOCATION */
        .container-kurir {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: #2c2c2c;
            border-radius: 8px;
        }

        /* Header Section */
        .header-kurir {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #333;
            padding: 15px;
            border-radius: 8px;
        }

        .header h2 {
            margin: 0;
        }

        .salin-link {
            color: #00b894;
            text-decoration: none;
        }

        .salin-link:hover {
            text-decoration: underline;
        }

        /* Timeline Section */
        .timeline {
            margin-top: 20px;
            padding: 0 20px;
            color: white;
        }

        .timeline-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .dot {
            width: 10px;
            height: 10px;
            background-color: #00b894;
            border-radius: 50%;
            margin-right: 15px;
        }

        .status {
            font-size: 14px;
        }

        /* Button */
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #00b894;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .btn:hover {
            background-color: #019874;
        }
    </style>

</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <a href="/Gear4Play_Shop/<?= htmlspecialchars($home_page) ?>"><img src="../assets/images/Logo.png" alt="Gear4Play_Shop Logo" class="logo"></a>
            <i class="bx bx-menu" id="btn_menu"></i>
            <nav class="content-sidebar">
                <ul>
                    <li class="<?= ($current_page == 'dashboard_admin.php') ? 'active' : ''; ?>">
                        <a href="dashboard_admin.php">
                            <i class="bx bx-package"></i>
                            <span class="nav-item">Dashboard</span>
                        </a>
                        <span class="tooltip">Dashboard</span>
                    </li>
                    <li class="<?= ($current_page == '#') ? 'active' : ''; ?>">
                        <a href="#">
                            <i class="fa-solid fa-scroll"></i>
                            <span class="nav-item">Kelola User</span>
                        </a>
                        <span class="tooltip">Kelola User</span>
                    </li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <!-- Header Section -->
            <header class="header">
                <div class="search-container">
                    <i class="bx bxs-search"></i>
                    <input type="text" placeholder="Search..." class="search_bar">
                </div>
                <div class="user-area">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="icons">
                            <a href="notifications.php" aria-label="Notifications">
                                <i class='bx bxs-bell'></i>
                                <span class="badge">5</span> <!-- Notifikasi dinamis -->
                            </a>
                            <a href="cart.php" aria-label="Cart">
                                <i class='bx bxs-basket'></i>
                                <span class="badge">2</span> <!-- Keranjang dinamis -->
                            </a>
                        </div>
                        <div class="profile-container">
                            <img src="../assets/images/Icon_Akun.svg" alt="User Profile" class="profile" id="profile-btn">
                            <div class="dropdown-menu" id="profile-dropdown">
                                <ul>
                                    <li><a href="#"><i class="fa-regular fa-user"></i> Profile</a></li>
                                    <li><a href="#"><i class="bx bx-wallet"></i> Wallet</a></li>
                                    <li><a href="#"><i class="fa-solid fa-gear"></i> Settings</a></li>
                                    <li><a href="#"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                                </ul>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="login-btn" aria-label="Login or Signup">Login/Signup</a>
                    <?php endif; ?>
                </div>
            </header>

            <h1 style="color: white; margin-top: 20px; margin-left: 8vh;">Order Detail</h1>

            <!-- Container Kurir -->
            <div class="container-kurir">
                <!-- Estimasi Sampai -->
                <div class="header-kurir">
                    <h2 style="color:white;">Estimasi Sampai: pada Senin, 15 April 2024</h2>
                    <p style="color:wheat;">No. Resi: #<?= htmlspecialchars($pengiriman_data['resi'] ?? 'N/A') ?> <a href="#" class="salin-link">SALIN</a></p>
                </div>

                <!-- Live Location -->
                <div class="timeline">
                    <?php
                    $live_locations = explode("\n", $pengiriman_data['live_location_item']); // Pisahkan lokasi berdasarkan baris baru
                    foreach ($live_locations as $location) {
                        echo '<div class="timeline-item">';
                        echo '<div class="dot"></div>';
                        echo '<div class="status">' . htmlspecialchars(trim($location)) . '</div>';
                        echo '</div>';
                    }
                    ?>
                </div>

                <!-- Bukti Pengiriman -->
                <?php if (!empty($pengiriman_data['proof_image'])): ?>
                    <a href="../uploads/<?= htmlspecialchars($pengiriman_data['proof_image']) ?>" class="btn" target="_blank">Lihat Bukti Pengiriman</a>
                <?php else: ?>
                    <p class="status" style="color: white;">Bukti pengiriman belum tersedia.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>

<!-- Buat sidebar -->
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
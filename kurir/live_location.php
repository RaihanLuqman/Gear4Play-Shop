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
$stmt = $conn->prepare("SELECT p.*, b.proof_image, b.proof_text FROM pengiriman p LEFT JOIN buktipengiriman b ON p.idPengiriman = b.idPengiriman WHERE p.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$pengiriman_result = $stmt->get_result();

if ($pengiriman_result->num_rows > 0) {
    $pengiriman_data = $pengiriman_result->fetch_assoc();
} else {
    die("Error: No data found for the provided order ID.");
}
$stmt->close();

// Proses Form: Update Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $status = $conn->real_escape_string($_POST['status']);
    $stmt = $conn->prepare("UPDATE pengiriman SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    header("Location: live_location.php?order_id=$order_id");
    exit();
}

// Proses Form: Update Live Location dengan format terbaru di atas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['live_location'])) {
    $new_location = $conn->real_escape_string($_POST['live_location']);
    $stmt = $conn->prepare("SELECT live_location_item FROM pengiriman WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    // Menambahkan lokasi terbaru di atas
    $updated_location = $new_location;
    if (!empty($data['live_location_item'])) {
        $updated_location .= ", " . $data['live_location_item'];
    }

    $stmt = $conn->prepare("UPDATE pengiriman SET live_location_item = ? WHERE order_id = ?");
    $stmt->bind_param("si", $updated_location, $order_id);
    $stmt->execute();
    header("Location: live_location.php?order_id=$order_id");
    exit();
}

// Proses Form: Upload Bukti
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['proof_image'])) {
    $target_dir = "../uploads/";
    $file_name = basename($_FILES["proof_image"]["name"]);
    $target_file = $target_dir . $file_name;
    if (move_uploaded_file($_FILES["proof_image"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO buktipengiriman (idPengiriman, proof_image) VALUES (?, ?) ON DUPLICATE KEY UPDATE proof_image = ?");
        $stmt->bind_param("iss", $pengiriman_data['idPengiriman'], $file_name, $file_name);
        $stmt->execute();
        header("Location: live_location.php?order_id=$order_id");
        exit();
    } else {
        echo "Error uploading file.";
    }
}
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
        /* Basic Reset */


        .timeline {
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .timeline-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .dot {
            width: 10px;
            height: 10px;
            background-color: #4CAF50;
            border-radius: 50%;
            margin-right: 10px;
        }

        .status {
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .status p {
            font-size: 16px;
            color: #555;
        }

        .update-status-form,
        .live-location-form {
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        label {
            font-size: 16px;
            margin-bottom: 5px;
            display: block;
        }

        textarea,
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        button {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        /* Media Queries */
        @media (max-width: 768px) {

            .timeline,
            .status,
            .update-status-form,
            .live-location-form {
                padding: 10px;
            }

            button {
                width: 100%;
            }
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
                            <div class="profile-info">
                                <img src="<?= $profile_picture ?>" class="profile-pic" id="profile-btn">
                                <div class="dropdown-menu" id="profile-dropdown">
                                    <ul>
                                        <li class="username"><?php echo htmlspecialchars($username); ?></li>
                                        <li><a href="#"><i class="fa-regular fa-user"></i> Profile</a></li>
                                        <li><a href="#"><i class="bx bx-wallet"></i> Wallet</a></li>
                                        <li><a href="chat_pelanggan.php"><i class='bx bx-message-rounded-dots'></i> Chat Admin</a></li>
                                        <li><a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="tulisan-login" aria-label="Login or Signup">
                            <img src="assets/images/Icon_Akun.svg" alt="Login/Register Icon">
                            Login/Signup
                        </a>
                    <?php endif; ?>
                </div>
            </header>
            <h2 style="color: white; margin-bottom:10px">Live Location</h2>
            <div class="container-kurir">
                <!-- Estimasi Sampai -->

                <!-- Live Location -->
                <div class="timeline">
                    <?php
                    $live_locations = explode(", ", $pengiriman_data['live_location_item']);
                    foreach ($live_locations as $index => $location) {
                        echo '<div class="timeline-item" style="color: white; margin-bottom:10px">';
                        echo '<div class="dot"></div>';
                        echo '<p><strong>Lokasi ' . (count($live_locations) - $index) . ':</strong> ' . htmlspecialchars($location) . '</p>';
                        echo '</div>';
                    }
                    ?>
                </div>

                <!-- Status Pengiriman -->
                <!-- Status Pengiriman -->
                <div class="status">
                    <?php
                    if ($pengiriman_data['status'] == 'pending') {
                        echo '<p>Menunggu paket di pickup kurir</p>';
                    } elseif ($pengiriman_data['status'] == 'processing') {
                        echo '<p>Paket di pickup kurir</p>';
                    } elseif ($pengiriman_data['status'] == 'shipped') {
                        echo '<p>Paket sedang dikirim</p>';
                    } elseif ($pengiriman_data['status'] == 'delivered') {
                        echo '<p>Paket telah sampai.</p>';
                    } elseif ($pengiriman_data['status'] == 'canceled') {
                        echo '<p>Paket dibatalkan</p>';
                    } else {
                        echo '<p>Status pengiriman tidak diketahui.</p>';
                    }
                    ?>
                </div>

                <!-- Form untuk Update Status Pengiriman -->
                <?php if ($pengiriman_data['status'] != 'delivered' && $pengiriman_data['status'] != 'canceled'): ?>
                    <form action="update_status.php" method="POST">
                        <label for="status" style="color:white">Ubah Status:</label>
                        <select name="status" id="status">
                            <option value="pending" <?= ($pengiriman_data['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?= ($pengiriman_data['status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
                            <option value="shipped" <?= ($pengiriman_data['status'] == 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?= ($pengiriman_data['status'] == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                            <option value="canceled" <?= ($pengiriman_data['status'] == 'canceled') ? 'selected' : ''; ?>>Canceled</option>
                        </select><br><br>
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id) ?>">
                        <button type="submit" class="btn" style="margin-bottom:10px">Update Status</button>
                    </form>
                <?php else: ?>
                    <p style="color:white">Status sudah "Delivered" atau "Canceled" dan tidak dapat diubah lagi.</p>
                <?php endif; ?>



                <!-- Form Update Live Location -->
                <form action="live_location.php?order_id=<?= htmlspecialchars($order_id) ?>" method="POST">
                    <label for="live_location" style="color:white">Tambah Lokasi Baru:</label>
                    <textarea name="live_location" rows="3" required></textarea><br>
                    <button type="submit">Tambah Lokasi</button>
                </form>


                <!-- Bukti Pengiriman -->
                <?php if (!empty($pengiriman_data['proof_image'])): ?>
                    <a href="../uploads/<?= htmlspecialchars($pengiriman_data['proof_image']) ?>" target="_blank">Lihat Bukti Pengiriman</a>
                <?php else: ?>
                    <p style="color:white">Bukti pengiriman belum tersedia.</p>
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
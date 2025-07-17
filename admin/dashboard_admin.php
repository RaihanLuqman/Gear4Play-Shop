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

// Periksa role user di database
$user_query = "SELECT username, profile_picture, role FROM user WHERE id_user = '$user_id'";
$user_result = $conn->query($user_query);

if ($user_result->num_rows > 0) {
    $user_data = $user_result->fetch_assoc();
    $role = $user_data['role'];

    // Periksa apakah user memiliki role admin
    if ($role !== 'admin') {
        // Jika bukan admin, arahkan ke halaman lain atau tampilkan pesan error
        header("Location: unauthorized.php"); // Halaman unauthorized
        exit();
    }

    $username = $user_data['username'];
    $profile_picture = '../assets/images/default-profile.png'; // Default

    if (!empty($user_data['profile_picture']) && file_exists('uploads/' . $user_data['profile_picture'])) {
        $profile_picture = 'uploads/' . $user_data['profile_picture'];
    }
} else {
    // Jika user tidak ditemukan, arahkan ke halaman login
    header("Location: login.php");
    exit();
}

// Query untuk mendapatkan total orders, total delivered, total canceled, dan total revenue
$query_total_orders = "SELECT COUNT(*) FROM orders";
$result_total_orders = mysqli_query($conn, $query_total_orders);
$total_orders = mysqli_fetch_array($result_total_orders)[0];

$query_total_delivered = "SELECT COUNT(*) FROM pengiriman WHERE status = 'delivered'";
$result_total_delivered = mysqli_query($conn, $query_total_delivered);
$total_delivered = mysqli_fetch_array($result_total_delivered)[0];

$query_total_canceled = "SELECT COUNT(*) FROM pengiriman WHERE status = 'canceled'";
$result_total_canceled = mysqli_query($conn, $query_total_canceled);
$total_canceled = mysqli_fetch_array($result_total_canceled)[0];

$query_total_revenue = "SELECT SUM(o.total_price) 
                        FROM orders o
                        JOIN pengiriman p ON o.order_id = p.order_id
                        WHERE p.status = 'delivered'";
$result_total_revenue = mysqli_query($conn, $query_total_revenue);
$total_revenue = mysqli_fetch_array($result_total_revenue)[0];

// Mengatur halaman saat ini
$current_page = basename($_SERVER['PHP_SELF']);
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
    <link rel="stylesheet" href="../assets/dashboard_admin.css">
    <script src="../includes/script_profile.js" defer></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />


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
                            <i class="bx bx-home-alt-2"></i>
                            <span class="nav-item">Dashboard</span>
                        </a>
                        <span class="tooltip">Dashboard</span>
                    </li>
                    <li class="<?= ($current_page == '#') ? 'active' : ''; ?>">
                        <a href="chat.php">
                            <i class="bx bx-user-circle"></i>
                            <span class="nav-item">Kelola User</span>
                        </a>
                        <span class="tooltip">Kelola User</span>
                    </li>
                    <li class="<?= ($current_page == 'dataproduk.php') ? 'active' : ''; ?>">
                        <a href="dataproduk.php">
                            <i class="bx bxs-search"></i>
                            <span class="nav-item">Data Produk</span>
                        </a>
                        <span class="tooltip">Data Produk</span>
                    </li>
                    <li class="<?= ($current_page == '#') ? 'active' : ''; ?>">
                        <a href="#">
                            <i class="bx bxs-wallet"></i>
                            <span class="nav-item">Transaksi</span>
                        </a>
                        <span class="tooltip">Transaksi</span>
                    </li>
                    <li class="<?= ($current_page == 'chat.php') ? 'active' : ''; ?>">
                        <a href="chat_admin.php">
                            <i class="bx bx-message-dots"></i>
                            <span class="nav-item">Chat</span>
                        </a>
                        <span class="tooltip">Chat</span>
                    </li>
                    <li class="<?= ($current_page == '#') ? 'active' : ''; ?>">
                        <a href="#">
                            <i class="bx bxs-star"></i>
                            <span class="nav-item">Ulasan</span>
                        </a>
                        <span class="tooltip">Ulasan</span>
                    </li>
                    <li class="<?= ($current_page == '#') ? 'active' : ''; ?>">
                        <a href="ads.php">
                            <i class="bx bx-link-external"></i>
                            <span class="nav-item">Iklan</span>
                        </a>
                        <span class="tooltip">Iklan</span>
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

            <h1 style="color: white;margin-top:20px;margin-left:8vh;">Dashboard</h1>
            <canvas id="myChart" style="width:100%;max-width:700px;margin-left:5%;color:white;margin-top:10px"></canvas>
            <script>
                const xValues = [50, 60, 70, 80, 90, 100, 110, 120, 130, 140, 150];
                const yValues = [7, 8, 8, 9, 9, 9, 10, 11, 14, 14, 15];

                new Chart("myChart", {
                    type: "line",
                    data: {
                        labels: xValues,
                        datasets: [{
                            fill: false,
                            lineTension: 0,
                            backgroundColor: "rgba(0,0,255,1.0)",
                            borderColor: "rgb(255,255,255)",
                            data: yValues
                        }]
                    },
                    options: {
                        legend: {
                            display: false
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    min: 6,
                                    max: 16
                                }
                            }],
                        }
                    }
                });
            </script>
            <div class="dashboard">
                <div class="card">
                    <div class="icon">
                        <img src="../assets/images/Group 148.png" alt="Total Orders">
                    </div>
                    <div class="content">
                        <h2 style="font-size:22px"><?= number_format($total_orders) ?></h2>
                        <p>Total Orders</p>
                        <span class="status positive">+4% (30 days)</span>
                    </div>
                </div>
                <div class="card">
                    <div class="icon">
                        <img src="../assets/images/Icon_Order.png" alt="Total Delivered">
                    </div>
                    <div class="content">
                        <h2 style="font-size:22px"><?= number_format($total_delivered) ?></h2>
                        <p>Total Delivered</p>
                        <span class="status positive">+4% (30 days)</span>
                    </div>
                </div>
                <div class="card">
                    <div class="icon">
                        <img src="../assets/images/icon Delivered.png" alt="Total Canceled">
                    </div>
                    <div class="content">
                        <h2 style="font-size:22px"><?= number_format($total_canceled) ?></h2>
                        <p>Total Canceled</p>
                        <span class="status negative">-26% (30 days)</span>
                    </div>
                </div>
                <div class="card">
                    <div class="icon">
                        <img src="../assets/images/Group 408.png" alt="Total Revenue">
                    </div>
                    <div class="content">
                        <h2 style="font-size:18px">Rp.<?= number_format($total_revenue, 2) ?></h2>
                        <p>Total Revenue</p>
                        <span class="status neutral">+12% (30 days)</span>
                    </div>
                </div>
            </div>


            <h2 style="color: white;margin-top:20px;margin-left:8vh;font-size:5vh">Customer Review</h2>

            <div class="review-container">
                <div class="review-card">
                    <div class="user-info">
                        <img src="../assets/images/WhatsApp Image 2024-12-06 at 10.53.34_fc5f6f3c.jpg" alt="User Profile" class="profile-pic">
                        <div>
                            <h4 class="user-name">Mamad Nunduk</h4>
                            <p class="review-time">1 hari yang lalu</p>
                        </div>
                    </div>
                    <div class="rating">
                        <span>⭐️⭐️⭐️⭐️</span>
                        <span class="rating-score">4.0</span>
                    </div>
                    <p class="variant">Varian: Warna Hitam</p>
                    <p class="review-text">Barangnya Original, Adminnya Fast Respon. Rekomend deh...</p>
                    <div class="footer">
                        <span class="store-name">Gear4Play</span>
                        <span class="product-name">ROG Strix G16</span>
                    </div>
                </div>
                <div class="review-card">
                    <div class="user-info">
                        <img src="../assets/images/WhatsApp Image 2024-12-06 at 10.53.34_fc5f6f3c.jpg" alt="User Profile" class="profile-pic">
                        <div>
                            <h4 class="user-name">Mamad Nunduk</h4>
                            <p class="review-time">1 hari yang lalu</p>
                        </div>
                    </div>
                    <div class="rating">
                        <span>⭐️⭐️⭐️⭐️</span>
                        <span class="rating-score">4.0</span>
                    </div>
                    <p class="variant">Varian: Warna Hitam</p>
                    <p class="review-text">Barangnya Original, Adminnya Fast Respon. Rekomend deh...</p>
                    <div class="footer">
                        <span class="store-name">Gear4Play</span>
                        <span class="product-name">ROG Strix G16</span>
                    </div>
                </div>
                <div class="review-card">
                    <div class="user-info">
                        <img src="../assets/images/WhatsApp Image 2024-12-06 at 10.53.34_fc5f6f3c.jpg" alt="User Profile" class="profile-pic">
                        <div>
                            <h4 class="user-name">Mamad Nunduk</h4>
                            <p class="review-time">1 hari yang lalu</p>
                        </div>
                    </div>
                    <div class="rating">
                        <span>⭐️⭐️⭐️⭐️</span>
                        <span class="rating-score">4.0</span>
                    </div>
                    <p class="variant">Varian: Warna Hitam</p>
                    <p class="review-text">Barangnya Original, Adminnya Fast Respon. Rekomend deh...</p>
                    <div class="footer">
                        <span class="store-name">Gear4Play</span>
                        <span class="product-name">ROG Strix G16</span>
                    </div>
                </div>
                <div class="review-card">
                    <div class="user-info">
                        <img src="../assets/images/WhatsApp Image 2024-12-06 at 10.53.34_fc5f6f3c.jpg" alt="User Profile" class="profile-pic">
                        <div>
                            <h4 class="user-name">Mamad Nunduk</h4>
                            <p class="review-time">1 hari yang lalu</p>
                        </div>
                    </div>
                    <div class="rating">
                        <span>⭐️⭐️⭐️⭐️</span>
                        <span class="rating-score">4.0</span>
                    </div>
                    <p class="variant">Varian: Warna Hitam</p>
                    <p class="review-text">Barangnya Original, Adminnya Fast Respon. Rekomend deh...</p>
                    <div class="footer">
                        <span class="store-name">Gear4Play</span>
                        <span class="product-name">ROG Strix G16</span>
                    </div>
                </div>
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
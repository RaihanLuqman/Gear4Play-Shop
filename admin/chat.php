<!-- <?php
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

$current_page = basename($_SERVER['PHP_SELF']);

// Fetch products from the database
$product_query = "SELECT * FROM product";
$product_result = $conn->query($product_query);

$product_query = "SELECT p.*, k.nama_kategori FROM product p JOIN kategori k ON p.idkategori = k.idKategori";
$product_result = $conn->query($product_query);

// Fetch billboard item from the database
$billboard_query = "SELECT b.*, p.product_name FROM billboard b JOIN product p ON b.id_product = p.id_product";
$billboard_result = $conn->query($billboard_query);
$billboard_item = $billboard_result->fetch_assoc();
// Ambil data billboard dari tabel billboard


$product_query = "SELECT p.*, k.nama_kategori 
                  FROM product p
                  JOIN kategori k ON p.idkategori = k.idKategori";
$product_result = $conn->query($product_query);


// Fetch billboard item from the database
// $billboard_query = "SELECT * FROM product WHERE is_featured = 1 LIMIT 1";
// $billboard_result = $conn->query($billboard_query);
// $billboard_item = $billboard_result->fetch_assoc();
// Ambil data billboard dari tabel billboard
// $billboard_query = "SELECT b.*, p.name AS product_name FROM billboard b JOIN product p ON b.id_product = p.id";
// $billboard_result = $conn->query($billboard_query);
?> -->

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
        .chat-container {
            margin: 20px;
            background-color: #2a2a2a;
            border-radius: 10px;
            padding: 20px;
            color: white;
        }

        .chat-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .chat-header input {
            padding: 10px;
            width: 300px;
            border: none;
            border-radius: 5px;
        }

        .chat-header button {
            padding: 10px;
            background-color: #00ff88;
            border: none;
            border-radius: 5px;
            color: black;
            cursor: pointer;
        }

        .chat-header button:hover {
            background-color: #00cc6e;
        }

        .chat-content {
            display: flex;
            gap: 20px;
        }

        .chat-list {
            width: 30%;
            background-color: #1e1e1e;
            color: white;
            padding: 10px;
            border-radius: 10px;
        }

        .chat-list .user {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #3a3a3a;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-list .user:hover {
            background-color: #00ff88;
            color: black;
        }

        .chat-box {
            flex: 1;
            background-color: #1e1e1e;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .messages {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .message {
            display: flex;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            max-width: 70%;
        }

        .message.sent {
            display: flex;
            background-color: #00ff88;
            color: black;
            align-items: flex-end;
            margin-left: 50vh;
        }

        .message.received {
            background-color: #3a3a3a;
            color: white;
            align-self: flex-start;
        }

        .input-container {
            display: flex;
            gap: 10px;
        }

        .input-container input {
            flex: 1;
            padding: 10px;
            border-radius: 5px;
            border: none;
        }

        .input-container button {
            padding: 10px;
            background-color: #00ff88;
            border: none;
            border-radius: 5px;
            color: black;
            cursor: pointer;
        }

        .input-container button:hover {
            background-color: #00cc6e;
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
                            <i class="bx bx-home-alt-2"></i>
                            <span class="nav-item">Dashboard</span>
                        </a>
                        <span class="tooltip">Dashboard</span>
                    </li>
                    <li class="<?= ($current_page == '#') ? 'active' : ''; ?>">
                        <a href="#">
                            <i class="bx bx-user-circle"></i>
                            <span class="nav-item">Kelola User</span>
                        </a>
                        <span class="tooltip">Kelola User</span>
                    </li>
                    <li class="<?= ($current_page == '#') ? 'active' : ''; ?>">
                        <a href="#">
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
                        <a href="chat.php">
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
                        <a href="#">
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
            <h1 style="color: white;margin-left:8vh;">Chat</h1>
            <div class="chat-container">
                <div class="chat-content">
                    <div class="chat-list">
                        <div class="user">
                            <span>Jono Sigma</span>
                            <span>Online</span>
                        </div>
                        <div class="user">
                            <span>Mamad Nunduk</span>
                            <span>Typing...</span>
                        </div>
                        <div class="user">
                            <span>Budi Itadori</span>
                            <span>Online</span>
                        </div>
                        <div class="user">
                            <span>Wan Satoru</span>
                            <span>Online</span>
                        </div>
                        <div class="user">
                            <span>Budiono Siregar</span>
                            <span>Offline</span>
                        </div>
                    </div>

                    <div class="chat-box">
                        <div class="messages">
                            <div class="message received">Halo min bisa rakit pc?</div>
                            <div class="message sent">Bisa, nih brp budgetnya?</div>
                            <div class="message received">Dikit aja min 4.5 jt</div>
                            <div class="message sent">Full Gemink rgb min!</div>
                            <div class="message received">Aman dana segitu mah!</div>
                        </div>

                        <div class="input-container">
                            <input type="text" placeholder="Start typing here">
                            <button>Send</button>
                        </div>
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
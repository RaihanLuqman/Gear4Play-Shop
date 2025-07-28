<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk</title>
    <link rel="stylesheet" href="data_produk.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="../assets/sidebar.css">
    <script src="../includes/script_profile.js" defer></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1E1E1E;
            margin: 0;
            padding: 0;
        }

        .content {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 24px;
            color: white;
        }

        .btn-add {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
        }

        .btn-add:hover {
            background-color: #45a049;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .product-card {
            background-color: #323232;
            padding: 20px;
            border: 1px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .product-card img {
            width: 90%;
            height: 250px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .product-info h3 {
            margin: 10px 0;
            font-size: 18px;
            color: #333;
        }

        .product-info p {
            margin: 5px 0;
            color: #777;
        }

        .category {
            font-style: italic;
            color: #888;
        }

        .btn-edit {
            background-color: #1EFF57;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            margin-top: 10px;
        }

        .btn-edit:hover {
            background-color: #1EFF57;
        }

        .no-products {
            font-size: 18px;
            text-align: center;
            color: #888;
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
                        <a href="chat.php">
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

        <div class="content">
            <div class="header">
                <h2>Data Produk</h2>
                <button class="btn-add" onclick="window.location.href='add_product.php'">
                    <i class="fas fa-plus"></i> Tambah Produk
                </button>
            </div>
            <div class="product-grid">
                <!-- PHP Loop to Fetch Products -->
                <?php
                // Koneksi ke database
                $conn = new mysqli('localhost', 'root', '', 'gear4play_shop');

                // Periksa koneksi
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Query untuk mengambil data produk
                $result = $conn->query("SELECT * FROM product");

                // Cek apakah ada produk
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Periksa apakah gambar ada di folder 'uploads'
                        $image_path = '../admin/' . $row['image_url'];
                        $product_name = htmlspecialchars($row['product_name']);
                        $product_price = number_format($row['price'], 0, ',', '.');
                        $product_category = htmlspecialchars($row['idkategori']);
                        $stock = htmlspecialchars($row['stock']);

                        // Outputkan HTML untuk produk
                        echo "<div class='product-card'>
                        <img src='$image_path' alt='$product_name'>
                        <div class='product-info'>
                            <h3 style='color: white;'>$product_name</h3>
                            <p>Rp$product_price</p>
                            <p class='category'>$product_category</p>
                            <p>Stock: $stock</p>
                            <button class='btn-edit' onclick='window.location.href=\"edit_product.php?id=" . $row['id_product'] . "\"'>
                                <i class='fas fa-pencil-alt'></i>
                            </button>
                        </div>
                    </div>";
                    }
                } else {
                    echo "<p class='no-products'>Belum ada produk yang ditambahkan.</p>";
                }

                // Tutup koneksi
                $conn->close();
                ?>
            </div>
        </div>
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

</html>
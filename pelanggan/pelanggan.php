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

// Ambil data user dari database
$user_query = "SELECT username, profile_picture FROM user WHERE id_user = '$user_id'";
$user_result = $conn->query($user_query);
$user_data = $user_result->fetch_assoc();

$username = $user_data['username'];
$profile_picture = '../assets/images/default-profile.png'; // Default

if (!empty($_SESSION['user_image']) && file_exists('uploads/' . $_SESSION['user_image'])) {
    $profile_picture = 'uploads/' . $_SESSION['user_image'];
}

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


#cart
$cart_count = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT SUM(ci.quantity) AS total_item 
            FROM cart c 
            JOIN cartitem ci ON c.id_cart = ci.id_cart
            WHERE c.id_pelanggan = $user_id";

    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        $cart_count = $row['total_item'] ?? 0;
    }
}


#search
$search = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $product_query = "SELECT p.*, k.nama_kategori 
                      FROM product p
                      JOIN kategori k ON p.idkategori = k.idKategori
                      WHERE p.product_name LIKE '%$search%'";
} else {
    $product_query = "SELECT p.*, k.nama_kategori 
                      FROM product p
                      JOIN kategori k ON p.idkategori = k.idKategori";
}

$product_result = $conn->query($product_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gear4Play</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Gear4Play_Shop/assets/style.css">
    <link rel="stylesheet" href="/Gear4Play_Shop/assets/sidebar.css">
    <script src="../includes/script_profile.js" defer></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .favorite-btn {
            background-color: transparent;
            /* Menghapus latar belakang */
            border: none;
            /* Menghapus border */
            padding: 0;
            /* Menghapus padding */
            display: inline-flex;
            /* Mengatur agar ikon berada di tengah */
            align-items: center;
            /* Menjaga ikon tetap di tengah */
            justify-content: center;
            /* Menjaga ikon tetap di tengah */
        }
    </style>

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
                    <li class="<?= ($current_page == 'order.php') ? 'active' : ''; ?>">
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
            <header class="header">
                <form method="GET" action="pelanggan.php" class="search-container">
                    <i class="bx bxs-search"></i>
                    <input type="text" name="search" placeholder="Search..." class="search_bar" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                </form>
                <div class="user-area">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="icons">
                            <a href="notifications.php" aria-label="Notifications">
                                <i style="color:#2ecc71;" class='bx bxs-bell'></i>
                                <span class="badge">5</span> <!-- Notifikasi dinamis -->
                            </a>
                            <a href="cart.php" aria-label="Cart">
                                <i style="color:#2ecc71;" class='bx bxs-basket'></i>
                                <span class="badge"><?= $cart_count ?></span> <!-- Keranjang dinamis -->
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

            <section class="billboard">
                <div class="slides-container">
                    <?php if ($billboard_result->num_rows > 0): ?>
                        <?php while ($row = $billboard_result->fetch_assoc()): ?>
                            <div class="slide">
                                <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="billboard-image">
                                <div class="slide-content">
                                    <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                                    <a href="product_detail.php?id=<?php echo $row['id_product']; ?>">
                                        <button>Order Now</button>
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="slide">
                            <p>No billboard items available.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="dots-container">
                    <!-- Dots will be generated dynamically using JavaScript -->
                </div>
            </section>


            <section class="products">
                <h2 style="color:white; margin-bottom:10px">Products</h2>
                <div class="product-list">
                    <?php if ($product_result->num_rows > 0): ?>
                        <?php while ($row = $product_result->fetch_assoc()):
                            // Membentuk path gambar dengan benar
                            $imagePath = '../admin/' . $row['image_url']; ?>
                            <div class="product">
                                <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                                <form action="add_to_wishlist.php" method="POST">
                                    <input type="hidden" name="id_product" value="<?= $row['id_product']; ?>">
                                    <input type="hidden" name="active" value="1"> <!-- 1 berarti like -->
                                    <button type="submit" name="add_to_wishlist" class="favorite-btn">
                                        <i class="bx bx-heart"></i>
                                    </button>
                                </form>
                                <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                                <p class="harga">Rp<?php echo number_format($row['price'], 0, ',', '.'); ?></p>
                                <p class="category"><?php echo htmlspecialchars($row['nama_kategori']); ?></p> <!-- Tampilkan kategori -->
                                <a href="product_detail_buyer.php?id=<?php echo $row['id_product']; ?>" class="detail-link">
                                    <i class='bx bx-chevron-right' style='color:#323232'></i>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color:white;">No products found.</p>
                    <?php endif; ?>
                </div>
            </section>
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

<!-- untuk billboard -->
<script>
    const slidesContainer = document.querySelector('.slides-container');
    const slides = document.querySelectorAll('.slide');
    const dotsContainer = document.querySelector('.dots-container');
    let currentIndex = 0;

    // Generate dots based on number of slides
    slides.forEach((_, index) => {
        const dot = document.createElement('span');
        dot.classList.add('dot');
        if (index === 0) dot.classList.add('active');
        dotsContainer.appendChild(dot);

        dot.addEventListener('click', () => {
            goToSlide(index);
        });
    });

    const dots = document.querySelectorAll('.dot');

    // Function to move to a specific slide
    function goToSlide(index) {
        slidesContainer.style.transform = `translateX(-${index * 100}%)`;
        dots.forEach(dot => dot.classList.remove('active'));
        dots[index].classList.add('active');
        currentIndex = index;
    }

    // Function to move to the next slide
    function nextSlide() {
        const nextIndex = (currentIndex + 1) % slides.length;
        goToSlide(nextIndex);
    }

    // Auto-slide every 7 seconds
    setInterval(nextSlide, 7000);
</script>


<!-- favorite    -->
<!-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        const favoriteButtons = document.querySelectorAll('.favorite-btn');

        favoriteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');

                // Toggle warna ikon
                this.classList.toggle('active');

                const isActive = this.classList.contains('active');

                // Kirim request ke server (AJAX)
                fetch('add_to_wishlist.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id_product: productId,
                            active: isActive
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Wishlist updated!');
                        } else {
                            alert('Gagal memperbarui wishlist!');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        });
    });
</script> -->


<!-- favorite -->
<!-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        const favoriteButtons = document.querySelectorAll('.favorite-btn');

        favoriteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.parentElement.getAttribute('data-id');

                // Pengecekan apakah user sudah login
                fetch('./check_login.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.logged_in) {
                            toggleFavorite(button, productId);
                        } else {
                            // Arahkan ke login jika belum login
                            window.location.href = 'login.php';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });
        });

        // Fungsi untuk toggle favorite
        function toggleFavorite(button, productId) {
            const isActive = button.classList.toggle('active');
            button.style.color = isActive ? '#1EFF00' : '#5e6168';

            fetch('wishlist_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_product: productId,
                        action: isActive ? 'add' : 'remove'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Wishlist updated!');
                    } else {
                        alert('Gagal memperbarui wishlist.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    });
</script> -->

<!-- drop down list profile -->
<!-- <script>
    document.addEventListener("DOMContentLoaded", function() {
        const profileBtn = document.getElementById('profile-btn');
        const profileDropdown = document.getElementById('profile-dropdown');

        if (profileBtn && profileDropdown) {
            profileBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                profileDropdown.classList.toggle('visible');
            });

            document.addEventListener('click', function() {
                profileDropdown.classList.remove('visible');
            });

            profileDropdown.addEventListener('click', function(event) {
                event.stopPropagation();
            });
        }
    });
</script> -->

</html>
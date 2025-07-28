<?php
// Connect to database
$conn = new mysqli('localhost', 'root', '', 'gear4play_shop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get product ID from URL
$id_product = isset($_GET['id']) ? intval($_GET['id']) : 0; // Perbaikan pada parameter GET
$product_query = "SELECT * FROM product WHERE id_product = $id_product";
$product_result = $conn->query($product_query);

// Redirect if product not found
if ($product_result->num_rows == 0) { // Mengubah logika pengecekan
    header("Location: index.php");
    exit();
}

$product = $product_result->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <title><?= htmlspecialchars($product['name']); ?> - Gear4Play</title> -->
    <title>Gear4Play</title>
    <link rel="stylesheet" href="/Gear4Play_Shop/assets/sidebar.css">
    <link rel="stylesheet" href="/Gear4Play_Shop/assets/product_detail.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <a href="/Gear4Play_Shop/<?= htmlspecialchars($home_page) ?>"><img src="/Gear4Play_Shop/assets/images/Logo.png" alt="Gear4Play_Shop Logo" class="logo"></a>
            <i class="bx bx-menu" id="btn_menu"></i>
            <nav class="content-sidebar">
                <ul>
                    <li class="<?= ($current_page == 'index.php') ? 'active' : ''; ?>">
                        <a href="index.php">
                            <i class="bx bxs-cart"></i>
                            <span class="nav-item">Store</span>
                        </a>
                        <span class="tooltip">Store</span>
                    </li>
                    <li class="<?= ($current_page == 'favorite.php') ? 'active' : ''; ?>">
                        <a href="favorite.php">
                            <i class="bx bxs-heart"></i>
                            <span class="nav-item">Favorite</span>
                        </a>
                        <span class="tooltip">Favorite</span>
                    </li>
                    <li class="<?= ($current_page == 'order.php') ? 'active' : ''; ?>">
                        <a href="order.php">
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
            <section class="detail_products">
                <div class="product-image">
                    <img src="<?php echo htmlspecialchars('admin/' . $product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                </div>

                <div class="cart-section">
                    <h3>Atur Jumlah dan Catatan</h3>
                    <div class="selected-color" style="margin-top: 10px;" id="selected-color-display">Hitam</div>
                    <div class="qty-controls">
                        <button id="decrease-btn">-</button>
                        <input type="number" id="qty-input" value="1" min="1" max="<?php echo htmlspecialchars($product['stock']); ?>">
                        <button id="increase-btn">+</button>
                    </div>
                    <p>Stok: <?php echo htmlspecialchars($product['stock']); ?></p>
                    <p id="subtotal">SubTotal: Rp<?php echo number_format($product['price'], 0, ',', '.'); ?></p>

                    <button>Tambah Keranjang</button>
                    <button>Beli</button>
                    <button>Tambah Favorit</button>
                </div>

                <div class="product-details">
                    <h1 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h1>
                    <p class="product-price">Rp<?php echo number_format($product['price'], 0, ',', '.'); ?></p>

                    <div class="product-actions">
                        <div class="selected-color" style="margin-top: 10px;">Pilih Warna: Hitam</div>
                        <div class="color-options">
                            <button type="button" class="color-btn active" data-color="Hitam">Hitam</button>
                            <button type="button" class="color-btn" data-color="Putih">Putih</button>
                        </div>
                    </div>

                    <div class="product-description">
                        <h2>Detail Produk</h2>
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>
                </div>

            </section>

            <h2 class="judul-review">Ulasan Pembeli</h2>
            <section class="review-section">
                <div class="rating">
                    <span class="rating-stars">★★★★☆</span>
                    <span>4.0/5.0</span>
                </div>
                <p>90% Pembeli Merasa Puas</p>
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

<!-- untuk option warna hitam/putih -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const colorButtons = document.querySelectorAll(".color-btn");
        const selectedColorTextProduct = document.querySelector(".selected-color"); // Teks 'Pilih Warna:' di product-actions
        const selectedColorTextCart = document.getElementById("selected-color-display"); // Teks di cart-section

        colorButtons.forEach((button) => {
            button.addEventListener("click", function() {
                // Hapus kelas 'active' dari semua tombol
                colorButtons.forEach((btn) => btn.classList.remove("active"));

                // Tambahkan kelas 'active' ke tombol yang diklik
                this.classList.add("active");

                // Perbarui teks warna terpilih di bagian 'product-actions' dan 'cart-section'
                const color = this.getAttribute("data-color");
                selectedColorTextProduct.textContent = "Pilih Warna: " + color; // Tetap menampilkan "Pilih Warna: ..."
                selectedColorTextCart.textContent = color; // Hanya menampilkan nama warna
            });
        });
    });
</script>

<!-- untuk qty -+ -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const decreaseBtn = document.getElementById("decrease-btn");
        const increaseBtn = document.getElementById("increase-btn");
        const qtyInput = document.getElementById("qty-input");
        const subtotalText = document.getElementById("subtotal");

        // Harga satuan produk
        const productPrice = <?php echo htmlspecialchars($product['price']); ?>;

        // Fungsi untuk memperbarui subtotal
        function updateSubtotal() {
            const qty = parseInt(qtyInput.value);
            const subtotal = qty * productPrice;
            subtotalText.textContent = `SubTotal: Rp${subtotal.toLocaleString('id-ID')}`;
        }

        // Event listener untuk tombol -
        decreaseBtn.addEventListener("click", function() {
            let qty = parseInt(qtyInput.value);
            if (qty > 1) {
                qty--;
                qtyInput.value = qty;
                updateSubtotal();
            }
        });

        // Event listener untuk tombol +
        increaseBtn.addEventListener("click", function() {
            let qty = parseInt(qtyInput.value);
            const maxStock = parseInt(qtyInput.max);
            if (qty < maxStock) {
                qty++;
                qtyInput.value = qty;
                updateSubtotal();
            }
        });

        // Event listener untuk input kuantitas langsung
        qtyInput.addEventListener("input", function() {
            let qty = parseInt(qtyInput.value);
            const maxStock = parseInt(qtyInput.max);

            // Validasi kuantitas
            if (isNaN(qty) || qty < 1) {
                qty = 1;
            } else if (qty > maxStock) {
                qty = maxStock;
            }

            qtyInput.value = qty;
            updateSubtotal();
        });
    });
</script>


</html>
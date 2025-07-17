<?php
// Connect to database
$conn = new mysqli('localhost', 'root', '', 'gear4play_shop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from the database
$product_query = "SELECT * FROM product";
$product_result = $conn->query($product_query);

// // Fetch billboard item from the database
// $billboard_query = "SELECT * FROM product WHERE is_featured = 1 LIMIT 1";
// $billboard_result = $conn->query($billboard_query);
// $billboard_item = $billboard_result->fetch_assoc();
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
    <link rel="stylesheet" href="/Gear4Play_Shop/assets/sidebar_tes.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>


</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <i class="bx bx-menu" id="btn_menu"></i>
            <a href="/Gear4Play_Shop/<?= htmlspecialchars($home_page) ?>">
                <img src="/Gear4Play_Shop/assets/images/Logo.png" alt="Gear4Play_Shop Logo" class="logo"></a>
            <nav>
                <ul>
                    <li>
                        <a href="#">
                            <i class="bx bxs-cart"></i>
                            <span class="nav-item">Store</span>
                        </a>
                        <span class="tooltip">Store</span>
                    </li>
                    <li>
                        <a href="#">
                            <i class="bx bxs-heart"></i>
                            <span class="nav-item">Favorite</span>
                        </a>
                        <span class="tooltip">Favorite</span>
                    </li>
                    <li>
                        <a href="#">
                            <i class="bx bxs-package"></i>
                            <span class="nav-item">My Order</span>
                        </a>
                        <span class="tooltip">My Order</span>
                    </li>
                </ul>
            </nav>
            <li class="category-toggle">
                <a href="#" id="category-toggle-btn">
                    <i class="bx bx-category"></i> Category
                    <i class="bx bx-chevron-down arrow"></i>
                </a>
                <ul class="category-list">
                    <li><a href="#">Computer</a></li>
                    <li><a href="#">Headset</a></li>
                    <li><a href="#">VR Glasses</a></li>
                    <li><a href="#">Mouse Gaming</a></li>
                    <li><a href="#">Keyboard</a></li>
                </ul>
            </li>
        </aside>

        <main class="main-content">
            <header class="header">
                <input type="text" placeholder="Search...">
                <button class="login-btn">Login/Signup /button>
            </header>

            <section class="billboard">
                <?php if ($billboard_item): ?>
                    <div class="billboard-item">
                        <h2><?php echo $billboard_item['name']; ?></h2>
                        <p><?php echo $billboard_item['description']; ?></p>
                        <button>Order Now</button>
                    </div>
                <?php else: ?>
                    <div class="billboard-item placeholder">
                        <p>No featured product</p>
                    </div>
                <?php endif; ?>
            </section>

            <section class="products">
                <h2>Products</h2>
                <div class="product-list">
                    <?php if ($product_result->num_rows > 0): ?>
                        <?php while ($row = $product_result->fetch_assoc()): ?>
                            <div class="product">
                                <img src="images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
                                <h3><?php echo $row['name']; ?></h3>
                                <p>Rp<?php echo number_format($row['price'], 0, ',', '.'); ?></p>
                                <button>Add to Favorite</button>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No products found.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <script src="script.js"></script> <!-- Include your JS file -->
</body>

<script>
    let btn_menu = document.querySelector('#btn_menu');
    let sidebar = document.querySelector('.sidebar');
    let mainContent = document.querySelector('.main-content');


    btn_menu.onclick = function() {
        sidebar.classList.toggle('active');
        mainContent.classList.toggle('active'); // Tambahkan toggle untuk main-content
    };
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    const categoryToggleBtn = document.getElementById('category-toggle-btn');
    const categoryToggle = document.querySelector('.category-toggle');

    categoryToggleBtn.addEventListener('click', function (e) {
        e.preventDefault(); // Prevent default link behavior
        categoryToggle.classList.toggle('active'); // Toggle active class
    });
});

</script>

</html>
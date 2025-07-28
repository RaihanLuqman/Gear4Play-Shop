<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'gear4play_shop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pastikan session id_user ada
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to access this page.");
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

// Fetch wishlist products
$query = "SELECT p.*, w.id_product FROM wishlist w JOIN product p ON w.id_product = p.id_product WHERE w.id_user = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Wishlist</title>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Gear4Play_Shop/assets/style.css">
    <link rel="stylesheet" href="/Gear4Play_Shop/assets/sidebar.css">
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
            <section class="wishlist">
                <h2 style="color:white">Your Wishlist</h2>
                <div class="wishlist-list">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="product">
                                <img src="../admin/<?= $row['image_url']; ?>" alt="<?= $row['product_name']; ?>">
                                <h3><?= htmlspecialchars($row['product_name']); ?></h3>
                                <p class="harga">Rp<?= number_format($row['price'], 0, ',', '.'); ?></p>
                                <form action="add_to_wishlist.php" method="POST">
                                    <input type="hidden" name="id_product" value="<?= $row['id_product']; ?>">
                                    <input type="hidden" name="active" value="0"> <!-- 0 berarti unlike -->
                                    <button type="submit" name="add_to_wishlist" class="favorite-btn">
                                        <i class="bx bx-heart"></i> Remove from Wishlist
                                    </button>
                                </form>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color:white">Your wishlist is empty.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
</body>

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

</html>
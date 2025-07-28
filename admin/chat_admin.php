<?php
session_start();

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'gear4play_shop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure session user_id exists
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the user is an admin
$admin_check = $conn->query("SELECT role FROM user WHERE id_user = $user_id");
$admin_row = $admin_check->fetch_assoc();
$is_admin = ($admin_row['role'] === 'admin');

// Handle form submission for sending a message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'], $_POST['user_id']) && $is_admin) {
    $message = $conn->real_escape_string($_POST['message']);
    $recipient_user_id = intval($_POST['user_id']);

    // Insert message into chat table
    $conn->query("INSERT INTO chat (message, sent_by, sent_to, timestamp) VALUES ('$message', '$user_id', '$recipient_user_id', NOW())");
}

// Get the list of users who have sent messages to the admin
$users_result = $conn->query("SELECT DISTINCT sent_by, u.username FROM chat c JOIN user u ON c.sent_by = u.id_user WHERE c.sent_to = $user_id");
$users = $users_result->fetch_all(MYSQLI_ASSOC);

// Get the chat history with a specific user
$current_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
$chat_messages = [];
if ($current_user_id) {
    $chat_result = $conn->query("SELECT * FROM chat WHERE (sent_by = $current_user_id AND sent_to = $user_id) OR (sent_by = $user_id AND sent_to = $current_user_id) ORDER BY timestamp ASC");
    $chat_messages = $chat_result->fetch_all(MYSQLI_ASSOC);
}
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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

        /* Daftar pengguna */
        .chat-list {
            width: 30%;
            background-color: #1e1e1e;
            color: white;
            padding: 10px;
            border-radius: 10px;
        }

        .chat-list ul {
            list-style: none;
            padding: 0;
        }

        .chat-list li {
            background-color: #3a3a3a;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .chat-list li:hover {
            background-color: #00ff88;
            color: black;
        }

        .chat-list li a {
            text-decoration: none;
            color: white;
            display: block;
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
            padding-right: 10px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            /* Jarak antar pesan */
        }

        .message {
            padding: 10px 15px;
            border-radius: 10px;
            /* Membuat bentuk kotak dengan sudut melengkung */
            max-width: 70%;
            /* Batas lebar pesan */
            word-wrap: break-word;
            /* Memastikan teks tetap dalam kotak */
            display: inline-block;
        }

        .message.sent {
            background-color: #00ff88;
            /* Warna hijau terang */
            color: black;
            /* Warna teks hitam */
            align-self: flex-end;
            /* Posisi di kanan */
            text-align: left;
        }

        .message.received {
            background-color: #3a3a3a;
            /* Warna abu-abu gelap */
            color: white;
            /* Warna teks putih */
            align-self: flex-start;
            /* Posisi di kiri */
            text-align: left;
        }

        .messages::-webkit-scrollbar {
            width: 6px;
        }

        .messages::-webkit-scrollbar-thumb {
            background-color: #444;
            border-radius: 5px;
        }

        .message .timestamp {
            font-size: 12px;
            color: #ccc;
            display: block;
            margin-top: 5px;
            /* Jarak kecil di bawah teks pesan */
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


    <!-- <style>
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        .container {
            display: flex;
            flex-direction: row;
            width: 100%;
        }

        .sidebar {
            background-color: #333;
            color: #fff;
            width: 250px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .sidebar .logo {
            width: 150px;
            margin-bottom: 20px;
        }

        .sidebar .user-list {
            list-style: none;
            padding: 0;
            width: 100%;
        }

        .sidebar .user-list li {
            width: 100%;
            margin-bottom: 10px;
        }

        .sidebar .user-list li a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 5px;
            background-color: #444;
        }

        .sidebar .user-list li a.active,
        .sidebar .user-list li a:hover {
            background-color: #555;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: #1e1e1e;
        }

        /* .header {
            background-color: #fff;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
        }

        .header h2 {
            margin: 0;
        } */

        .chat-container {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        .chat-sidebar {
            width: 250px;
            background-color: #f1f1f1;
            border-right: 1px solid #ddd;
            overflow-y: auto;
        }

        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-history {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .chat-message {
            margin-bottom: 20px;
        }

        .chat-message.sent {
            text-align: right;
        }

        .chat-message p {
            display: inline-block;
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
        }

        .chat-message.sent p {
            background-color: #dcf8c6;
        }

        .chat-message.received p {
            background-color: #fff;
            border: 1px solid #ddd;
        }

        .chat-message .timestamp {
            display: block;
            font-size: 12px;
            color: #aaa;
            margin-top: 5px;
        }

        .chat-form {
            display: flex;
            padding: 20px;
            background-color: #fff;
            border-top: 1px solid #ddd;
        }

        .chat-form textarea {
            flex: 1;
            resize: none;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-right: 10px;
        }

        .chat-form button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #333;
            color: #fff;
            cursor: pointer;
        }
    </style> -->
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
                                        <li><a href="#"><i class='bx bx-message-rounded-dots'></i> Chat Admin</a></li>
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
            <h1 style="color: white;margin-left:8vh;">Chat Admin</h1>
            <div class="chat-container">
                <div class="chat-content">
                    <!-- Daftar Pengguna -->
                    <div class="chat-list">
                        <ul>
                            <?php foreach ($users as $user): ?>
                                <li>
                                    <a href="chat_admin.php?user_id=<?= $user['sent_by'] ?>" class="<?= ($current_user_id === $user['sent_by']) ? 'active' : '' ?>">
                                        <?= htmlspecialchars($user['username']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Kotak Chat -->
                    <div class="chat-box">
                        <div class="messages">
                            <?php if ($current_user_id): ?>
                                <?php foreach ($chat_messages as $message): ?>
                                    <div class="message <?= ($message['sent_by'] === $admin_id) ? 'sent' : 'received' ?>">
                                        <p><?= htmlspecialchars($message['message']) ?></p>
                                        <span class="timestamp"><?= date('Y-m-d H:i:s', strtotime($message['timestamp'])) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Select a user to view the chat history.</p>
                            <?php endif; ?>
                        </div>

                        <?php if ($current_user_id): ?>
                            <form action="chat_admin.php?user_id=<?= $current_user_id ?>" method="POST" class="chat-form">
                                <div class="input-container">
                                    <input type="hidden" name="user_id" value="<?= $current_user_id ?>">
                                    <input name="message" placeholder="Type your message..." required>
                                    <button type="submit">Send</button>
                                </div>
                            </form>
                        <?php endif; ?>
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


<!-- real time chat -->
<script>
    $(document).ready(function() {
        function fetchMessages() {
            var current_user_id = <?= isset($current_user_id) ? $current_user_id : 'null' ?>;
            if (current_user_id) {
                $.ajax({
                    url: '../includes/reply_fast_A.php',
                    method: 'GET',
                    data: {
                        user_id: current_user_id
                    },
                    success: function(data) {
                        var messages = JSON.parse(data);
                        var chatHistory = $('.messages');
                        chatHistory.empty();

                        messages.forEach(function(message) {
                            var messageClass = (message.sent_by == <?= $user_id ?>) ? 'sent' : 'received';
                            var messageElement = '<div class="chat-message ' + messageClass + '">' +
                                '<p>' + message.message + '</p>' +
                                '<span class="timestamp">' + new Date(message.timestamp).toLocaleString() + '</span>' +
                                '</div>';
                            chatHistory.append(messageElement);
                        });

                        // Scroll to the bottom of chat history
                        chatHistory.scrollTop(chatHistory.prop("scrollHeight"));
                    }
                });
            }
        }

        // Poll messages every 2 seconds
        setInterval(fetchMessages, 2000);

        // Fetch messages immediately when the page loads
        fetchMessages();
    });
</script>

</html>
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
$admin_id = 4; // Admin user_id is 4

// Handle form submission for sending a message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = $conn->real_escape_string($_POST['message']);

    // Insert message into chat table
    $conn->query("INSERT INTO chat (message, sent_by, sent_to, timestamp) VALUES ('$message', '$user_id', '$admin_id', NOW())");
}

// Retrieve chat history
$chat_result = $conn->query("SELECT * FROM chat WHERE (sent_by = $user_id AND sent_to = $admin_id) OR (sent_by = $admin_id AND sent_to = $user_id) ORDER BY timestamp ASC");
$chat_messages = $chat_result->fetch_all(MYSQLI_ASSOC);
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
                    <div class="chat-box">
                        <div class="messages">
                            <?php
                            // Retrieve messages between the user and the admin
                            $result = $conn->query("SELECT * FROM chat WHERE (sent_by = $user_id AND sent_to = 4) OR (sent_by = 4 AND sent_to = $user_id) ORDER BY timestamp ASC");

                            while ($row = $result->fetch_assoc()) {
                                $class = $row['sent_by'] == $user_id ? 'sent' : 'received';
                                // Convert timestamp to a more readable format
                                $timestamp = date('Y-m-d H:i:s', strtotime($row['timestamp']));
                                echo "<div class='message $class'>
                                <p>{$row['message']}</p>
                                <span class='timestamp'>$timestamp</span>
                              </div>";
                            }
                            ?>
                        </div>
                        <form method="POST" action="">
                            <div class="input-container">
                                <input type="text" name="message" placeholder="Type a message..." required>
                                <button type="submit">Send</button>
                            </div>
                        </form>
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
            var current_user_id = <?= isset($user_id) ? $user_id : 'null' ?>;
            if (current_user_id) {
                $.ajax({
                    url: '../includes/reply_fast_P.php',
                    method: 'GET',
                    data: {
                        user_id: current_user_id
                    },
                    success: function(data) {
                        var messages = JSON.parse(data);
                        var chatBox = $('.messages');
                        chatBox.empty();

                        messages.forEach(function(message) {
                            var messageClass = (message.sent_by == current_user_id) ? 'sent' : 'received';
                            var messageElement = '<div class="message ' + messageClass + '">' +
                                '<p>' + message.message + '</p>' +
                                '<span class="timestamp">' + new Date(message.timestamp).toLocaleString() + '</span>' +
                                '</div>';
                            chatBox.append(messageElement);
                        });

                        // Scroll to the bottom of chat history
                        chatBox.scrollTop(chatBox.prop("scrollHeight"));
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
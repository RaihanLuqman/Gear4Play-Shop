<?php
session_start();

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to login page if the user is not logged in or not an admin
    header("Location: login.php");
    exit();
}

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'gear4play_shop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$admin_id = 4; // Admin user_id is 4

// Handle form submission for sending a message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'], $_POST['user_id'])) {
    $message = $conn->real_escape_string($_POST['message']);
    $user_id = intval($_POST['user_id']);

    // Insert message into chat table
    $conn->query("INSERT INTO chat (message, sent_by, sent_to, timestamp) VALUES ('$message', '$admin_id', '$user_id', NOW())");
}

// Get the list of users who have sent messages
$users_result = $conn->query("SELECT DISTINCT sent_by FROM chat WHERE sent_to = $admin_id");
$users = $users_result->fetch_all(MYSQLI_ASSOC);

// Optionally, get the chat history with a specific user if needed
if (isset($_GET['user_id'])) {
    $selected_user_id = intval($_GET['user_id']);
    $chat_result = $conn->query("SELECT * FROM chat WHERE (sent_by = $selected_user_id AND sent_to = $admin_id) OR (sent_by = $admin_id AND sent_to = $selected_user_id) ORDER BY timestamp ASC");
    $chat_messages = $chat_result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Chat - Gear4Play</title>
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
                                <li><a href="#">Controller</a></li>
                            </div>
                        </ul>
                    </li>
                    <li class="<?= ($current_page == 'history.php') ? 'active' : ''; ?>">
                        <a href="history.php">
                            <i class="bx bx-history"></i>
                            <span class="nav-item">History</span>
                        </a>
                        <span class="tooltip">History</span>
                    </li>
                    <li class="<?= ($current_page == 'profile.php') ? 'active' : ''; ?>">
                        <a href="profile.php">
                            <i class="bx bxs-user"></i>
                            <span class="nav-item">Profile</span>
                        </a>
                        <span class="tooltip">Profile</span>
                    </li>
                </ul>
            </nav>
        </aside>
        <main>
            <h2>Admin Chat</h2>
            <div class="chat-container">
                <div class="chat-header">
                    <form method="post" action="">
                        <input type="hidden" name="admin_id" value="<?= htmlspecialchars($admin_id) ?>">
                    </form>
                </div>
                <div class="chat-content">
                    <div class="chat-list">
                        <h3>Users</h3>
                        <?php foreach ($users as $user) {
                            $user_id = $user['sent_by'];
                            // Get user name from database
                            $user_name_result = $conn->query("SELECT username FROM user WHERE user_id = $user_id");
                            $user_name_row = $user_name_result->fetch_assoc();
                            $user_name = $user_name_row['username'] ?? 'Unknown User';
                        ?>
                            <div class="user">
                                <span><?= htmlspecialchars($user_name) ?></span>
                                <a href="admin_chat.php?user_id=<?= $user_id ?>">Chat</a>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="chat-box">
                        <?php if (isset($_GET['user_id'])) {
                            $user_id = intval($_GET['user_id']);
                        ?>
                            <div class="messages">
                                <?php
                                // Fetch messages between admin and the selected user
                                $chat_result = $conn->query("SELECT * FROM chat WHERE (sent_by = $admin_id AND sent_to = $user_id) OR (sent_by = $user_id AND sent_to = $admin_id) ORDER BY timestamp ASC");
                                while ($chat_row = $chat_result->fetch_assoc()) {
                                    $message_class = ($chat_row['sent_by'] == $admin_id) ? 'sent' : 'received';
                                ?>
                                    <div class="message <?= $message_class ?>">
                                        <?= htmlspecialchars($chat_row['message']) ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <form method="post" action="">
                                <div class="input-container">
                                    <input type="hidden" name="user_id" value="<?= $user_id ?>">
                                    <input type="text" name="message" placeholder="Type your message here..." required>
                                    <button type="submit">Send</button>
                                </div>
                            </form>
                        <?php } else { ?>
                            <p>Select a user to start chatting</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

<script>
$(document).ready(function() {
    function fetchMessages() {
        var current_user_id = <?= isset($current_user_id) ? $current_user_id : 'null' ?>;
        if (current_user_id) {
            $.ajax({
                url: 'get_chat_messages.php',
                method: 'GET',
                data: { user_id: current_user_id },
                success: function(data) {
                    var messages = JSON.parse(data);
                    var chatHistory = $('.chat-history');
                    chatHistory.empty();
                    
                    messages.forEach(function(message) {
                        var messageClass = (message.sent_by == <?= $user_id ?>) ? 'sent' : 'received';
                        var messageElement = '<div class="chat-message ' + messageClass + '">'
                            + '<p>' + message.message + '</p>'
                            + '<span class="timestamp">' + new Date(message.timestamp).toLocaleString() + '</span>'
                            + '</div>';
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

<?php
$conn->close();
?>

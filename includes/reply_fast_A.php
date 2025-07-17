<?php
session_start();

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'gear4play_shop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure session user_id exists
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$current_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

if ($current_user_id) {
    // Get the chat history with the specific user
    $chat_result = $conn->query("SELECT * FROM chat WHERE (sent_by = $current_user_id AND sent_to = $user_id) OR (sent_by = $user_id AND sent_to = $current_user_id) ORDER BY timestamp ASC");
    $chat_messages = $chat_result->fetch_all(MYSQLI_ASSOC);

    echo json_encode($chat_messages);
} else {
    echo json_encode([]);
}

// Close the database connection
$conn->close();
?>

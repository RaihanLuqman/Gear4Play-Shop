<?php
// get_user_chat_messages.php
session_start();
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'gear4play_shop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_GET['user_id'];

if ($user_id) {
    $result = $conn->query("SELECT * FROM chat WHERE (sent_by = $user_id AND sent_to = 4) OR (sent_by = 4 AND sent_to = $user_id) ORDER BY timestamp ASC");

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    echo json_encode($messages);
}
?>

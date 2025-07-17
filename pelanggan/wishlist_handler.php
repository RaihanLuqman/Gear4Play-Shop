<?php
session_start();
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'gear4play_shop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

// Ambil data yang dikirim oleh AJAX
$input = json_decode(file_get_contents('php://input'), true);
$id_product = $input['id_product'];
$action = $input['action'];

if ($action == 'add') {
    // Menambahkan ke wishlist
    $stmt = $conn->prepare("INSERT INTO wishlist (id_user, id_product) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $id_product);
    $stmt->execute();
    echo json_encode(['success' => true]);
} elseif ($action == 'remove') {
    // Menghapus dari wishlist
    $stmt = $conn->prepare("DELETE FROM wishlist WHERE id_user = ? AND id_product = ?");
    $stmt->bind_param("ii", $user_id, $id_product);
    $stmt->execute();
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>

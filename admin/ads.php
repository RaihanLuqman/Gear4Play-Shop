<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'gear4play_shop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $id_product = $_POST['id_product'];

    // Handle file upload
    $image = $_FILES['image']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO billboard (title, description, image, id_product) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $title, $description, $image, $id_product);

    if ($stmt->execute()) {
        echo "Billboard uploaded successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Billboard</title>
</head>
<body>
    <h1>Upload Billboard</h1>
    <form action="ads.php" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" name="title" required><br><br>

        <label for="description">Description:</label>
        <textarea name="description" required></textarea><br><br>

        <label for="id_product">Product ID:</label>
        <input type="number" name="id_product" required><br><br>

        <label for="image">Image:</label>
        <input type="file" name="image" accept="image/*" required><br><br>

        <button type="submit">Upload</button>
    </form>
</body>
</html>

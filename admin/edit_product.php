<?php
// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'gear4play_shop');

// Periksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil ID produk dari URL
if (isset($_GET['id'])) {
    $id_product = $_GET['id'];

    // Query untuk mengambil data produk berdasarkan ID
    $query = "SELECT * FROM product WHERE id_product = $id_product";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        echo "Produk tidak ditemukan.";
        exit();
    }
} else {
    echo "ID produk tidak ditemukan.";
    exit();
}

// Proses update produk setelah form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $stock = $_POST['stock']; // Ambil data stok dari form

    // Query untuk memperbarui data produk
    $update_query = "UPDATE product SET product_name = '$product_name', price = $price, idkategori = '$category', stock = $stock WHERE id_product = $id_product";

    if ($conn->query($update_query) === TRUE) {
        echo "Produk berhasil diperbarui!";
        header('Location: dataproduk.php'); // Arahkan kembali ke halaman data produk
    } else {
        echo "Error: " . $conn->error;
    }
}

// Tutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="edit_product.css"> <!-- Menambahkan file CSS -->

    <style>
        /* CSS untuk desain halaman edit produk */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            color: #333;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button[type="submit"] {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        button[type="submit"]:focus {
            outline: none;
        }

        @media screen and (max-width: 768px) {
            .container {
                margin: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Edit Produk</h2>
        <form action="edit_product.php?id=<?= $id_product ?>" method="POST">
            <div class="form-group">
                <label for="product_name">Nama Produk:</label>
                <input type="text" id="product_name" name="product_name" value="<?= htmlspecialchars($product['product_name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="price">Harga:</label>
                <input type="number" id="price" name="price" value="<?= $product['price'] ?>" required>
            </div>

            <div class="form-group">
                <label for="category">Kategori:</label>
                <input type="text" id="category" name="category" value="<?= htmlspecialchars($product['idkategori']) ?>" required>
            </div>

            <div class="form-group">
                <label for="stock">Stok:</label>
                <input type="number" id="stock" name="stock" value="<?= $product['stock'] ?>" required>
            </div>

            <button type="submit" class="btn-submit">Perbarui Produk</button>
        </form>
    </div>
</body>

</html>
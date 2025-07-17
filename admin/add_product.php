<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="data_produk.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        /* CSS untuk desain halaman edit produk */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .content {
            max-width: 600px;
            width: 100%;
            margin: 20px;
            background-color: #fff;
            padding: 30px;
            border: 2px solid #ccc;
            /* Border yang lebih tegas untuk kotak */
            border-radius: 8px;
            /* Rounded corners */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            /* Shadow untuk efek kedalaman */
            box-sizing: border-box;
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
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            /* Padding lebih besar agar input lebih terasa luas */
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-top: 5px;
            /* Spasi antara label dan input */
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
            margin-top: 20px;
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
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="content">
        <div class="header">
            <h2>Tambah Produk</h2>
        </div>
        <form action="add_product.php" method="POST" enctype="multipart/form-data" class="form-add-product">
            <div class="form-group">
                <label for="name">Nama Produk</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="price">Harga Produk</label>
                <input type="number" id="price" name="price" required>
            </div>
            <div class="form-group">
                <label for="description">Deskripsi Produk</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="stock">Stok Produk</label>
                <input type="number" id="stock" name="stock" min="1" required>
            </div>
            <div class="form-group">
                <label for="category">Kategori Produk</label>
                <select id="category" name="category" required>
                    <option value="1">Keyboard</option>
                    <option value="2">Mouse</option>
                    <option value="3">Headset</option>
                    <option value="4">Monitor</option>
                    <option value="5">Mouse Pad</option>
                    <option value="6">Controller</option>
                    <option value="7">Gaming Chair</option>
                    <option value="8">Console</option>
                    <option value="9">VR Glasses</option>
                    <option value="10">Microphone</option>
                    <option value="11">Webcam</option>
                    <option value="12">Hardware</option>
                    <option value="13">Computer</option>
                    <option value="14">Laptop</option>
                    <option value="15">Gadget</option>
                </select>
            </div>
            <div class="form-group">
                <label for="image">Gambar Produk</label>
                <input type="file" id="image" name="image" accept="image/*" required>
            </div>
            <button type="submit" name="submit" class="btn-submit">Tambah Produk</button>
        </form>
    </div>

    <?php
    if (isset($_POST['submit'])) {
        // Connect to database
        $conn = new mysqli('localhost', 'root', '', 'gear4play_shop');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $name = $conn->real_escape_string($_POST['name']);
        $price = $conn->real_escape_string($_POST['price']);
        $description = $conn->real_escape_string($_POST['description']);
        $stock = (int)$_POST['stock'];
        $category = (int)$_POST['category']; // Ambil idkategori

        // Handle file upload
        $targetDir = "uploaded_image/";
        $targetFile = $targetDir . basename($_FILES['image']['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if directory exists, if not create it
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            // Insert data into database
            $sql = "INSERT INTO product (idkategori, product_name, description, price, stock, image_url) 
                    VALUES ('$category', '$name', '$description', '$price', '$stock', '$targetFile')";

            if ($conn->query($sql) === TRUE) {
                echo "<p class='success-message'>Produk berhasil ditambahkan.</p>";
            } else {
                echo "<p class='error-message'>Error: " . $conn->error . "</p>";
            }
        } else {
            echo "<p class='error-message'>Sorry, there was an error uploading your file.</p>";
        }

        $conn->close();
    }
    ?>
</body>

</html>
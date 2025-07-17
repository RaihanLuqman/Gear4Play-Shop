<?php
// Include koneksi database
include('includes/db_connect.php');

$success_message = ""; // Variabel untuk notifikasi sukses

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address_line = $_POST['address_line'];
    $city = $_POST['city'];
    $postal_code = $_POST['postal_code'];

    $role = 'pelanggan';

    // Insert data ke tabel user
    $user_query = "INSERT INTO user (username, password, email, phone_number, role) 
                   VALUES ('$username', '$password', '$email', '$phone_number', '$role')";

    if (mysqli_query($conn, $user_query)) {
        $id_user = mysqli_insert_id($conn);

        // Insert ke tabel address
        $address_query = "INSERT INTO address (id_user, address_line, city, postal_code) 
                          VALUES ('$id_user', '$address_line', '$city', '$postal_code')";

        if (mysqli_query($conn, $address_query)) {
            // Set notifikasi berhasil
            $success_message = "Pendaftaran Berhasil! Anda akan diarahkan ke halaman login.";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="assets/register.css"> <!-- Tautkan ke file CSS -->
    <script>
        // Redirect setelah 5 detik jika pendaftaran berhasil
        setTimeout(function () {
            <?php if (!empty($success_message)) : ?>
                window.location.href = 'login.php';
            <?php endif; ?>
        }, 5000);
    </script>
</head>

<body>
    <div class="container">
        <h2>Register</h2>

        <!-- Success message -->
        <?php if (!empty($success_message)): ?>
            <div class="success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="phone_number">Phone Number</label>
            <input type="tel" id="phone_number" name="phone_number" required pattern="\d+" title="Masukkan angka">

            <label for="address_line">Alamat Lengkap</label>
            <textarea id="address_line" name="address_line" rows="3" required></textarea>

            <label for="city">Kota</label>
            <input type="text" id="city" name="city" required>

            <label for="postal_code">Kode Pos</label>
            <input type="text" id="postal_code" name="postal_code" required pattern="\d+" title="Masukkan angka">

            <button type="submit">Register</button>
        </form>

        <div class="signup-link">
            <p>Already have an account? <a href="login.php">Sign in</a></p>
        </div>
    </div>
</body>

</html>

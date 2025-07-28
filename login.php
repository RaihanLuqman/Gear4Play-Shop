<?php
// Include koneksi database
include('includes/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $input = $_POST['input']; // Bisa berupa username, email, atau phone number
    $password = $_POST['password'];

    // Query untuk mencari user berdasarkan username, email, atau phone number
    $query = "SELECT * FROM user WHERE username = '$input' OR email = '$input' OR phone_number = '$input'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect berdasarkan role
            switch ($user['role']) {
                case 'admin':
                    header('Location: admin/dashboard_admin.php');
                    break;
                case 'pelanggan':
                    header('Location: pelanggan/pelanggan.php');
                    break;
                case 'kurir':
                    header('Location: kurir/dashboard_kurir.php');
                    break;
                default:
                    header('Location: dashboard.php'); // Default jika role tidak dikenali
                    break;
            }
            exit();
        } else {
            $error_message = "Incorrect password.";
        }
    } else {
        $error_message = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/login.css"> <!-- Tautkan ke file CSS -->
</head>

<body>
    <div class="login-container">
        <h2>Login</h2>

        <!-- Error message -->
        <?php if (!empty($error_message)): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="login.php" method="POST">
            <input type="text" id="input" name="input" placeholder="Username / Email / Phone Number" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="signup-link">
            <p>Don't have an account? <a href="register.php">Sign up</a></p>
        </div>
    </div>
</body>

<script>
    // Hapus pesan error setelah 5 detik
    setTimeout(function() {
        let errorDiv = document.querySelector('.error');
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    }, 5000);
</script>

</html>
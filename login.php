<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Ambil data user berdasarkan email
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND is_approved=1");
    $user = mysqli_fetch_assoc($query);

    if ($user) {
        $hashed_password = $user['password'];

        // Cek apakah password menggunakan password_hash()
        if (password_verify($password, $hashed_password)) {
            $valid_password = true;
        }
        // Jika tidak cocok, cek apakah password masih dalam format MD5
        elseif ($hashed_password == md5($password)) {
            $valid_password = true;

            // Update password ke format password_hash() untuk keamanan
            $new_hashed_password = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET password='$new_hashed_password' WHERE email='$email'");
        } else {
            $valid_password = false;
        }

        if ($valid_password) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Redirect berdasarkan peran
            if ($user['role'] == 'admin') {
                header("Location: admin/admin_dashboard.php");
            } elseif ($user['role'] == 'guru') {
                header("Location: guru/dashboard_guru.php");
            } elseif ($user['role'] == 'pelajar') {
                header("Location: pelajar/dashboard_pelajar.php");
            }
            exit;
        } else {
            $error_message = "Password salah! Coba lagi.";
        }
    } else {
        $error_message = "Email tidak ditemukan atau belum disetujui admin.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="container">
        <div class="image-section">
            <img src="images/bg.jpg" alt="Background Image">
        </div>
        <div class="login-section">
            <!-- Tambahkan logo di sini -->
            <img src="images/gensa.jpg" alt="GenSa Logo" class="logo">
            <h2>GenSa</h2>
            <h3>English & Arabic Center</h3>
            <?php if (isset($error_message)) {
                echo "<p class='error'>$error_message</p>";
            } ?>
            <form method="POST">
                <label>Username :</label>
                <input type="email" name="email" required>
                <label>Password :</label>
                <input type="password" name="password" required>
                <button type="submit">Login</button>
            </form>
            <p>Belum memiliki akun? <a href="register.php">Daftar di sini</a></p>
            <p>Butuh bantuan? <a href="contact_admin.php">Tanya Admin</a></p>
        </div>
    </div>
</body>

</html>
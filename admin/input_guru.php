<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
  header('Location: ../login.php');
  exit;
}

if (isset($_POST['add_teacher'])) {
  $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  // Proses Upload Foto
  $target_dir = "../uploads/";
  $foto_name = basename($_FILES["foto"]["name"]);
  $target_file = $target_dir . time() . "_" . $foto_name; // Rename file dengan timestamp
  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
  $allowed_types = array("jpg", "png", "jpeg");

  if (in_array($imageFileType, $allowed_types)) {
    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
      $foto_path = "uploads/" . time() . "_" . $foto_name;
    } else {
      echo "Gagal mengupload foto.";
      exit;
    }
  } else {
    echo "Format file tidak didukung. Hanya JPG, JPEG, dan PNG.";
    exit;
  }

  // Simpan data ke database
  $sql = "INSERT INTO users (nama_lengkap, email, password, role, is_approved, foto) 
          VALUES ('$nama', '$email', '$password', 'guru', 1, '$foto_path')";

  if (mysqli_query($conn, $sql)) {
    echo "Guru berhasil ditambahkan!";
  } else {
    echo "Error: " . mysqli_error($conn);
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Input User Guru</title>
  <link rel="stylesheet" href="../css/admin/input_guru.css">
  <style>

  </style>
</head>

<body>
  <!-- Button to toggle sidebar -->
  <button class="menu-toggle" onclick="toggleSidebar()">Menu</button>

  <div class="sidebar" id="sidebar">
    <h2>Admin Panel</h2>

    <?php
    // Dapatkan nama file halaman saat ini
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>

    <a href="admin_dashboard.php" class="<?= $current_page == 'admin_dashboard.php' ? 'active' : '' ?>">Dashboard</a>
    <a href="acc_user.php" class="<?= $current_page == 'acc_user.php' ? 'active' : '' ?>">ACC User</a>
    <a href="input_guru.php" class="<?= $current_page == 'input_guru.php' ? 'active' : '' ?>">Input User Guru</a>
    <a href="input_nilai.php" class="<?= $current_page == 'input_nilai.php' ? 'active' : '' ?>">Input Nilai</a>
    <a href="acc_absen_guru.php" class="<?= $current_page == 'acc_absen_guru.php' ? 'active' : '' ?>">ACC Absen Guru</a>
    <a href="../logout.php" class="logout-button" style="background-color: red;">Logout</a>
  </div>

  <div class="content">
    <div class="form-card">
      <h2>Input User Guru</h2>
      <form method="POST" enctype="multipart/form-data">
        <label>Nama Lengkap:</label>
        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required><br>
        <label>Email:</label>
        <input type="email" name="email" placeholder="Email" required><br>
        <label>Password:</label>
        <input type="password" name="password" placeholder="Password" required><br>
        <label>Foto:</label>
        <input type="file" name="foto" accept="image/*" required onchange="previewImage(event)">
        <img id="foto-preview" src="#" alt="Pratinjau Gambar" style="display: none; width: 100px; height: auto; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">
        <button type="submit" name="add_teacher">Tambah Guru</button>
      </form>
    </div>
  </div>

  <script src="../js/admin/input_guru.js">

  </script>
</body>

</html>
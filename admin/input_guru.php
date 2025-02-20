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
  <link rel="stylesheet" href="../style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      display: flex;
    }

    .profile {
      display: flex;
      align-items: center;
      border-bottom: 2px solid #ddd;
      margin-bottom: 20px;
      padding-bottom: 20px;
    }

    .profile img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      aspect-ratio: 1;
      border: 2px solid white;
      margin-right: 15px;
    }

    .profile h3 {
      font-size: 18px;
      margin: 0;
      word-wrap: break-word;
      white-space: normal;
      line-height: 1.2;
    }

    ul {
      padding: 0;
      margin: 0;
      list-style-type: none;
    }

    .sidebar {
      width: 250px;
      height: 100vh;
      background-color: #fbb117;
      color: white;
      padding: 20px;
      position: fixed;
      left: 0;
      top: 0;
      bottom: 0;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      transition: transform 0.3s ease;
      transition: background-color 0.3s;
      height: calc(100vh - 20px);
    }

    .sidebar a {
      display: block;
      color: white;
      text-decoration: none;
      padding: 10px;
      margin-bottom: 10px;
      background-color: #fbb117;
      text-align: center;
      border-radius: 5px;
      font-size: 16px;
      font-weight: bold;
      transition: background-color 0.3s;
    }

    .sidebar a:hover {
      background-color: brown;
    }

    .sidebar a.active {
      background-color: brown;
    }

    .logout-button {
      background-color: red;
      text-align: center;
      margin-top: auto;
      padding: 10px;
      border-radius: 5px;
      font-weight: bold;
      text-decoration: none;
      color: white;
    }

    .content {
      margin-left: 300px;
      padding: 20px;
      width: calc(100% - 270px);
    }

    .menu-toggle {
      display: none;
      padding: 10px;
      background-color: #fbb117;
      color: white;
      border: none;
      border-radius: 5px;
      position: fixed;
      top: 10px;
      left: 10px;
      z-index: 1000;
      cursor: pointer;
    }

    .menu-toggle:hover {
      background-color: brown;
    }

    .form-card {
      background-color: #ffffff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
      max-width: 500px;
      margin: 0 auto;
    }

    /* Judul form */
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }

    /* Label form */
    label {
      display: block;
      font-size: 16px;
      font-weight: bold;
      margin-bottom: 5px;
      color: #333;
    }

    /* Input field */
    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="file"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 14px;
      box-sizing: border-box;
    }

    /* Tombol submit */
    button[type="submit"] {
      background-color: #fbb117;
      color: white;
      font-size: 16px;
      font-weight: bold;
      padding: 12px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
      transition: background-color 0.3s ease;
    }

    /* Efek hover pada tombol */
    button[type="submit"]:hover {
      background-color: brown;
    }

    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }

      .sidebar.active {
        transform: translateX(0);
      }

      .content {
        margin-left: 0;
        width: 100%;
      }

      .menu-toggle {
        display: block;
      }
    }
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

  <script>
    // Function to toggle sidebar visibility
    function toggleSidebar() {
      document.querySelector('.sidebar').classList.toggle('active');
    }

    function previewImage(event) {
      var file = event.target.files[0]; // Ambil file yang dipilih
      if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
          // Tampilkan pratinjau gambar
          var imgPreview = document.getElementById('foto-preview');
          imgPreview.src = e.target.result;
          imgPreview.style.display = 'block'; // Tampilkan gambar pratinjau
        };
        reader.readAsDataURL(file); // Baca file sebagai Data URL
      }
    }
  </script>
</body>

</html>
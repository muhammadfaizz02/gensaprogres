<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      display: flex;
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
      /* Menjaga tombol logout di bagian bawah */
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

    form {
      max-width: 600px;
      /* Batas lebar form agar terlihat lebih rapi */
      margin: 20px 0;
      padding: 20px;
      background-color: #f9f9f9;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    form label {
      display: block;
      font-weight: bold;
      margin-bottom: 5px;
    }

    form input[type="text"],
    form input[type="email"],
    form input[type="password"],
    form select,
    form textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 16px;
      box-sizing: border-box;
    }

    form button {
      background-color: #fbb117;
      color: white;
      padding: 10px 15px;
      border: none;
      font-size: 16px;
      font-weight: bold;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
      /* Tombol akan mengambil seluruh lebar form */
    }

    form button:hover {
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

      form {
        padding: 10px;
      }

      form button {
        font-size: 14px;
        padding: 8px;
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
    <h2>Selamat Datang, Admin</h2>
    <p>Pilih menu di sidebar untuk mengelola pengguna dan nilai pelajar.</p>

    <!-- Notifikasi jumlah user yang perlu di-ACC -->
    <?php
    include '../config.php';

    // Hitung jumlah user yang belum di-approve
    $query_pending_users = mysqli_query($conn, "SELECT COUNT(*) AS total_pending FROM users WHERE is_approved = 0");
    $result = mysqli_fetch_assoc($query_pending_users);
    $total_pending_users = $result['total_pending'];
    ?>

    <h3>Notifikasi</h3>
    <p>Ada <?= $total_pending_users; ?> pengguna yang perlu di-ACC. <a href="acc_user.php">Lihat dan ACC User</a></p>
  </div>

  <script>
    // Function to toggle sidebar visibility
    function toggleSidebar() {
      document.querySelector('.sidebar').classList.toggle('active');
    }
  </script>
</body>

</html>